<?php
require_once __DIR__ . '/EquipmentInventoryCheck.php';
require_once __DIR__ . '/../connection.php';

class EquipmentInventoryCheckContext {
    public static function getAll(): array {
        $records = [];
        $conn = OpenConnection();
        $sql = "SELECT * FROM EquipmentInventoryCheck";
        $result = $conn->query($sql);

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $records[] = new EquipmentInventoryCheck(
                $row['equipment_id'],
                $row['inventory_check_id'],
                $row['checked_by_user_id'],
                $row['comment'],
                (bool)$row['check']
            );
        }

        return $records;
    }
    
    public static function getByCheckId(int $checkId): array {
        $conn = OpenConnection();
        // We only want those equipment that actually have a row in EquipmentInventoryCheck
        $sql = "
          SELECT
            e.id                   AS equipment_id,
            e.name                 AS equipment_name,
            COALESCE(ic.`check`,0) AS checked,
            COALESCE(ic.comment,'')AS comment,
            CONCAT(
              u.last_name, ' ',
              u.first_name,
              IF(
                u.middle_name IS NOT NULL AND u.middle_name <> '',
                CONCAT(' ', u.middle_name),
                ''
              )
            ) AS user_fullname
          FROM `EquipmentInventoryCheck` ic
          JOIN `Equipment` e
            ON e.id = ic.equipment_id
          LEFT JOIN `User` u
            ON u.id = ic.checked_by_user_id
          WHERE ic.inventory_check_id = ?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$checkId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function add(EquipmentInventoryCheck $item): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("
            INSERT INTO EquipmentInventoryCheck 
              (equipment_id, inventory_check_id, checked_by_user_id, comment, `check`)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $item->equipment_id,
            $item->inventory_check_id,
            $item->checked_by_user_id,
            $item->comment,
            $item->check
        ]);
    }

    public static function update(EquipmentInventoryCheck $item): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("
            UPDATE EquipmentInventoryCheck
               SET checked_by_user_id = ?, comment = ?, `check` = ?
             WHERE equipment_id = ? AND inventory_check_id = ?
        ");
        $stmt->execute([
            $item->checked_by_user_id,
            $item->comment,
            $item->check,
            $item->equipment_id,
            $item->inventory_check_id
        ]);
    }

    public static function delete(int $equipment_id, int $inventory_check_id): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("
            DELETE FROM EquipmentInventoryCheck 
             WHERE equipment_id = ? AND inventory_check_id = ?
        ");
        $stmt->execute([$equipment_id, $inventory_check_id]);
    }
}
