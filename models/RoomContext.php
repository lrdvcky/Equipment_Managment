<?php
require_once __DIR__ . '/Room.php';
require_once __DIR__ . '/../connection.php';

class RoomContext {
    /** @return Room[] */
    public static function getAll(): array {
        $db = OpenConnection();
        $sql = "SELECT id, name, short_name, responsible_user_id, temporary_responsible_user_id FROM `Room` ORDER BY id";
        $stmt = $db->query($sql);
        $out = [];
        while($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $out[] = new Room(
                (int)$r['id'],
                $r['name'],
                $r['short_name'],
                $r['responsible_user_id']   !== null ? (int)$r['responsible_user_id'] : null,
                $r['temporary_responsible_user_id'] !== null ? (int)$r['temporary_responsible_user_id'] : null
            );
        }
        return $out;
    }

    /** @return int */
    public static function create(string $name, ?string $short, ?int $resp, ?int $temp): int {
        $db = OpenConnection();
        $stmt = $db->prepare("
            INSERT INTO `Room`
              (name, short_name, responsible_user_id, temporary_responsible_user_id)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$name, $short, $resp, $temp]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, string $name, ?string $short, ?int $resp, ?int $temp): void {
        $db = OpenConnection();
        $stmt = $db->prepare("
            UPDATE `Room` SET
              name = ?, short_name = ?, responsible_user_id = ?, temporary_responsible_user_id = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, $short, $resp, $temp, $id]);
    }

    public static function delete(int $id): void {
        $db   = OpenConnection();
        $stmt = $db->prepare("DELETE FROM `Room` WHERE id = ?");
        try {
            $stmt->execute([$id]);
        } catch (\PDOException $e) {
            // SQLSTATE 23000 — нарушение FK
            if ($e->getCode() === '23000') {
                throw new \Exception(
                    "Невозможно удалить аудиторию №{$id}: к ней привязано оборудование"
                );
            }
            // остальные исключения пробрасываем дальше
            throw $e;
        }
    }
}
