<?php
require_once 'EquipmentSoftware.php';
require_once '../connection.php';

class EquipmentSoftwareContext {
    public static function getAll(): array {
        $records = [];
        $conn = OpenConnection();
        $sql = "SELECT * FROM EquipmentSoftware";
        $result = $conn->query($sql);

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $records[] = new EquipmentSoftware(
                $row['equipment_id'],
                $row['software_id']
            );
        }

        return $records;
    }

    public static function add(EquipmentSoftware $item): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("INSERT INTO EquipmentSoftware (equipment_id, software_id) VALUES (?, ?)");
        $stmt->execute([
            $item->equipment_id,
            $item->software_id
        ]);
    }

    public static function update(EquipmentSoftware $item, int $old_equipment_id, int $old_software_id): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("UPDATE EquipmentSoftware SET equipment_id = ?, software_id = ? WHERE equipment_id = ? AND software_id = ?");
        $stmt->execute([
            $item->equipment_id,
            $item->software_id,
            $old_equipment_id,
            $old_software_id
        ]);
    }

    public static function delete(int $equipment_id, int $software_id): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("DELETE FROM EquipmentSoftware WHERE equipment_id = ? AND software_id = ?");
        $stmt->execute([$equipment_id, $software_id]);
    }
}
?>
