<?php
require_once 'EquipmentInventoryCheck.php';
require_once '../connection.php';

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
        $sql = "
          SELECT
            ic.equipment_id,
            e.name AS equipment_name,
            ic.checked_by_user_id,
            -- собираем полное имя пользователя
            CONCAT(
              u.last_name, ' ',
              u.first_name,
              IF(u.middle_name IS NOT NULL AND u.middle_name <> '',
                 CONCAT(' ', u.middle_name),
                 '')
            ) AS user_fullname,
            ic.comment,
            ic.`check`
          FROM EquipmentInventoryCheck AS ic
          LEFT JOIN Equipment AS e
            ON e.id = ic.equipment_id
          -- здесь правильное имя таблицы
          LEFT JOIN `User` AS u
            ON u.id = ic.checked_by_user_id
          WHERE ic.inventory_check_id = ?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$checkId]);
        // вернём ассоциативный массив результатов
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function add(EquipmentInventoryCheck $item): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("INSERT INTO EquipmentInventoryCheck (equipment_id, inventory_check_id, checked_by_user_id, comment, `check`) VALUES (?, ?, ?, ?, ?)");
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
        $stmt = $conn->prepare("UPDATE EquipmentInventoryCheck SET checked_by_user_id = ?, comment = ?, `check` = ? WHERE equipment_id = ? AND inventory_check_id = ?");
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
        $stmt = $conn->prepare("DELETE FROM EquipmentInventoryCheck WHERE equipment_id = ? AND inventory_check_id = ?");
        $stmt->execute([$equipment_id, $inventory_check_id]);
    }
}
?>
