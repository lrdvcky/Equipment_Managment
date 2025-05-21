<?php
require_once '../user_model/Equipment.php';
require_once '../connection.php';

class EquipmentContext {
    public static function getUserEquipment($userId): array {
        $conn = OpenConnection();
        $stmt = $conn->prepare("
            SELECT 
                e.id, e.name, e.inventory_number, r.short_name as room_name, e.comment
            FROM Equipment e
            LEFT JOIN Room r ON e.room_id = r.id
            WHERE e.responsible_user_id = ?
        ");
        $stmt->execute([$userId]);

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Equipment(
                $row['id'],
                $row['name'],
                $row['inventory_number'],
                $row['room_name'],
                $row['comment']
            );
        }
        return $result;
    }
}
