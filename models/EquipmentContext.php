<?php
// models/EquipmentContext.php

require_once __DIR__ . '/../connection.php';
require_once __DIR__ . '/Equipment.php';

class EquipmentContext {

    /** @return Equipment[] */
    public static function getAll(): array {
        $conn = OpenConnection();
        $sql = "
            SELECT
                e.*,
                r.name AS room_name,
                u1.last_name AS resp_last, u1.first_name AS resp_first, u1.middle_name AS resp_middle,
                u2.last_name AS temp_last, u2.first_name AS temp_first, u2.middle_name AS temp_middle
            FROM `Equipment` e
            LEFT JOIN `Room` r ON e.room_id = r.id
            LEFT JOIN `User` u1 ON e.responsible_user_id = u1.id
            LEFT JOIN `User` u2 ON e.temporary_responsible_user_id = u2.id
        ";
        $stmt = $conn->query($sql);
        $out = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Фото
            $photoData = null;
            if (!empty($row['photo'])) {
                $photoData = 'data:image/jpeg;base64,' . base64_encode($row['photo']);
            }
            // Ответственные
            $respName = trim("{$row['resp_last']} {$row['resp_first']} {$row['resp_middle']}");
            $tempName = trim("{$row['temp_last']} {$row['temp_first']} {$row['temp_middle']}");

            $out[] = new Equipment(
                (int)$row['id'],
                $row['name'],
                $photoData,
                $row['inventory_number'],
                $row['room_id'] !== null ? (int)$row['room_id'] : null,
                $row['room_name'],
                $row['responsible_user_id'] !== null ? (int)$row['responsible_user_id'] : null,
                $respName ?: null,
                $row['temporary_responsible_user_id'] !== null ? (int)$row['temporary_responsible_user_id'] : null,
                $tempName ?: null,
                $row['price'] !== null ? (float)$row['price'] : null,
                // model_id убран
                null,
                $row['model_name'] ?? null,
                $row['comment'],
                $row['direction_name'],
                $row['status'],
                $row['equipment_type'],
                $row['inventory_section']
            );
        }
        return $out;
    }

    /**
     * Возвращает одну запись по id для сравнения старого responsable
     * @param int $id
     * @return object|null
     */
    public static function getById(int $id) {
        $conn = OpenConnection();
        $stmt = $conn->prepare("SELECT * FROM `Equipment` WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }

    /**
     * Создаёт новую запись
     * @param array $data
     * @return int — новый ID
     */
    public static function create(array $data): int {
        $conn = OpenConnection();
        $stmt = $conn->prepare("
            INSERT INTO Equipment (
                name,
                inventory_number,
                room_id,
                responsible_user_id,
                temporary_responsible_user_id,
                price,
                model_name,
                direction_name,
                status,
                comment,
                equipment_type,
                photo,
                inventory_section
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['name'],
            $data['inventory_number'],
            $data['room_id'],
            $data['responsible_user_id'],
            $data['temporary_responsible_user_id'],
            $data['price'],
            $data['model_name'],
            $data['direction_name'],
            $data['status'],
            $data['comment'],
            $data['equipment_type'],
            $data['photo'],
            $data['inventory_section']
        ]);
        return (int)$conn->lastInsertId();
    }

    /**
     * Обновляет запись
     * @param int $id
     * @param array $data
     */
    public static function update(int $id, array $data): void {
        $conn = OpenConnection();

        // Базовый SQL
        $sql = "
            UPDATE Equipment SET
              name                       = ?,
              inventory_number           = ?,
              room_id                    = ?,
              responsible_user_id        = ?,
              temporary_responsible_user_id = ?,
              price                      = ?,
              model_name                 = ?,
              direction_name             = ?,
              status                     = ?,
              comment                    = ?,
              equipment_type             = ?,
              inventory_section          = ?
        ";
        $params = [
            $data['name'],
            $data['inventory_number'],
            $data['room_id'],
            $data['responsible_user_id'],
            $data['temporary_responsible_user_id'],
            $data['price'],
            $data['model_name'],
            $data['direction_name'],
            $data['status'],
            $data['comment'],
            $data['equipment_type'],
            $data['inventory_section']
        ];

        // Если загрузили новое фото — добавляем
        if (array_key_exists('photo', $data)) {
            $sql .= ", photo = ?";
            $params[] = $data['photo'];
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
    }

    /** @param int $id */
    public static function delete(int $id): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("DELETE FROM `Equipment` WHERE id = ?");
        $stmt->execute([$id]);
    }
    /**
     * Проверить, есть ли в БД запись с таким inventory_number
     * @param int $inv
     * @return bool
     */
    public static function existsInventoryNumber(int $inv): bool {
        $conn = OpenConnection();
        $stmt = $conn->prepare("SELECT COUNT(*) FROM `Equipment` WHERE inventory_number = ?");
        $stmt->execute([$inv]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
