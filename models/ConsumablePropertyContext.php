
<?php
// models/ConsumablePropertyContext.php
require_once __DIR__ . '/../connection.php';
require_once __DIR__ . '/ConsumableProperty.php';

class ConsumablePropertyContext {
    public static function getAll(): array {
        $pdo = OpenConnection();
        $stmt = $pdo->query("SELECT * FROM `ConsumableProperty`");
        $list = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $list[] = new ConsumableProperty(
                $row['id'],
                (int)$row['consumable_id'],
                $row['property_name'],
                $row['property_value']
            );
        }
        return $list;
    }

    public static function add(ConsumableProperty $property): bool {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO ConsumableProperty (consumable_id, property_name, property_value) VALUES (?, ?, ?)");
        return $stmt->execute([$property->consumable_id, $property->property_name, $property->property_value]);
    }

    public static function update(ConsumableProperty $property): bool {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE ConsumableProperty SET consumable_id = ?, property_name = ?, property_value = ? WHERE id = ?");
        return $stmt->execute([$property->consumable_id, $property->property_name, $property->property_value, $property->id]);
    }

    public static function delete(int $id): bool {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM ConsumableProperty WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
