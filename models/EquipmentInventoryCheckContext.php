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
