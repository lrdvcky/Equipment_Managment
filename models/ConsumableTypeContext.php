<?php
require_once '../connection.php';
require_once 'ConsumableType.php';

class ConsumableTypeContext {
    public static function getAll(): array {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM ConsumableType");
        $types = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $types[] = new ConsumableType(
                $row['id'],
                $row['name']
            );
        }
        return $types;
    }

    public static function add(ConsumableType $type): bool {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO ConsumableType (name) VALUES (?)");
        return $stmt->execute([$type->name]);
    }

    public static function update(ConsumableType $type): bool {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE ConsumableType SET name = ? WHERE id = ?");
        return $stmt->execute([$type->name, $type->id]);
    }

    public static function delete(int $id): bool {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM ConsumableType WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
