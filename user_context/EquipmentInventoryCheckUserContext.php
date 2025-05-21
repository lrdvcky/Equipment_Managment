<?php
require_once '../connection.php';

class EquipmentInventoryCheckUserContext {

    public static function getByUser($userId) {
        $pdo = OpenConnection();

        $stmt = $pdo->prepare("
            SELECT e.name, r.name AS room_name, eic.comment, eic.check, e.id AS equipment_id
            FROM EquipmentInventoryCheck eic
            JOIN Equipment e ON e.id = eic.equipment_id
            LEFT JOIN Room r ON e.room_id = r.id
            WHERE eic.checked_by_user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function submitCheck($data) {
        $pdo = OpenConnection();

        $stmt = $pdo->prepare("
            UPDATE EquipmentInventoryCheck
            SET comment = ?, `check` = ?
            WHERE equipment_id = ? AND checked_by_user_id = ?
        ");
        return $stmt->execute([
            $data['comment'],
            $data['check'],
            $data['equipment_id'],
            $data['checked_by_user_id']
        ]);
    }
}
