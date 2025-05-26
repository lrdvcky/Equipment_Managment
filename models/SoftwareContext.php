<?php
// models/SoftwareContext.php

require_once __DIR__ . '/Software.php';
require_once __DIR__ . '/../connection.php';

class SoftwareContext {

    /** @return Software[] */
    public static function getAll(): array {
        $conn = OpenConnection();
        // Собираем в одну строку все имена оборудования через '||'
        $sql = "
          SELECT 
            s.*,
            GROUP_CONCAT(e.name SEPARATOR '||') AS eq_list
          FROM `Software` s
          LEFT JOIN `SoftwareEquipment` se ON s.id = se.software_id
          LEFT JOIN `Equipment` e           ON e.id = se.equipment_id
          GROUP BY s.id
          ORDER BY s.id
        ";
        $stmt = $conn->query($sql);
        $out = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $list = [];
            if (!empty($row['eq_list'])) {
                $list = explode('||', $row['eq_list']);
            }
            $out[] = new Software(
                (int)$row['id'],
                $row['name'],
                $row['version']        ?? null,
                $row['developer_name'] ?? null,
                $list
            );
        }
        return $out;
    }

    /** @return int — ID новой записи */
    public static function create(array $d): int {
        $conn = OpenConnection();
        $stmt = $conn->prepare("
            INSERT INTO `Software` (name, version, developer_name)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([
            $d['name'],
            $d['version']        ?? null,
            $d['developer_name'] ?? null
        ]);
        $newId = (int)$conn->lastInsertId();
        // назначаем оборудование, если есть
        if (!empty($d['equipment_ids']) && is_array($d['equipment_ids'])) {
            self::setEquipment($newId, $d['equipment_ids']);
        }
        return $newId;
    }

    public static function update(int $id, array $d): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("
            UPDATE `Software`
               SET name = ?, version = ?, developer_name = ?
             WHERE id = ?
        ");
        $stmt->execute([
            $d['name'],
            $d['version']        ?? null,
            $d['developer_name'] ?? null,
            $id
        ]);
        // обновляем связь с оборудованием
        if (isset($d['equipment_ids']) && is_array($d['equipment_ids'])) {
            self::setEquipment($id, $d['equipment_ids']);
        }
    }

    public static function delete(int $id): void {
        $conn = OpenConnection();
        // сначала связи
        $conn->prepare("DELETE FROM `SoftwareEquipment` WHERE software_id = ?")
             ->execute([$id]);
        // потом сам софт
        $stmt = $conn->prepare("DELETE FROM `Software` WHERE id = ?");
        try {
            $stmt->execute([$id]);
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new \Exception(
                    "Невозможно удалить программу: она используется где-то ещё"
                );
            }
            throw $e;
        }
    }

    /**
     * Сбрасывает и затем назначает оборудование для software_id
     * @param int $softwareId
     * @param int[] $equipIds
     */
    private static function setEquipment(int $softwareId, array $equipIds): void {
        $conn = OpenConnection();
        // сброс
        $conn->prepare("DELETE FROM `SoftwareEquipment` WHERE software_id = ?")
             ->execute([$softwareId]);
        // вставка новых
        $stmt = $conn->prepare("
            INSERT INTO `SoftwareEquipment` (software_id, equipment_id)
            VALUES (?, ?)
        ");
        foreach ($equipIds as $eid) {
            $stmt->execute([$softwareId, (int)$eid]);
        }
    }
}
