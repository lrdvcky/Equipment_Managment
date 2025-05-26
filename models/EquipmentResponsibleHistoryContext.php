<?php
// models/EquipmentResponsibleHistoryContext.php

require_once __DIR__ . '/../connection.php';
require_once __DIR__ . '/EquipmentResponsibleHistory.php';

class EquipmentResponsibleHistoryContext {

    /**
     * Добавляет запись в историю смен ответственных
     * @param int    $equipId
     * @param int    $userId
     * @param string $comment
     */
    public static function add(int $equipId, int $userId, string $comment): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("
            INSERT INTO EquipmentResponsibleHistory
              (equipment_id, user_id, comment)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$equipId, $userId, $comment]);
    }

    /**
     * Возвращает массив EquipmentResponsibleHistory для данного оборудования
     * @param int $equipId
     * @return EquipmentResponsibleHistory[]
     */
    public static function getByEquipment(int $equipId): array {
        $conn = OpenConnection();
        $stmt = $conn->prepare("
            SELECT id, equipment_id, user_id, comment, changed_at
            FROM EquipmentResponsibleHistory
            WHERE equipment_id = ?
            ORDER BY changed_at DESC
        ");
        $stmt->execute([$equipId]);

        $out = [];
        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $out[] = new EquipmentResponsibleHistory(
                (int)$r['id'],
                (int)$r['equipment_id'],
                (int)$r['user_id'],
                $r['comment'],
                $r['changed_at']
            );
        }
        return $out;
    }
}
