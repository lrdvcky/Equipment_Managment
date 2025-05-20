<?php
// models/ConsumableTypeContext.php
require_once __DIR__ . '/../connection.php';
require_once __DIR__ . '/ConsumableType.php';

class ConsumableTypeContext {
    public static function getAll(): array {
        $pdo = OpenConnection();
        $stmt = $pdo->query("SELECT * FROM `ConsumableType`");
        $list = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $list[] = new ConsumableType(
                $row['id'],
                $row['name']
            );
        }
        return $list;
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
