<?php
require_once '../connection.php';
require_once 'Consumable.php';

class ConsumableContext {
    public static function getAll(): array {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM Consumable");
        $consumables = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $consumables[] = new Consumable(
                $row['id'],
                $row['name'],
                $row['description'],
                $row['arrival_date'],
                $row['image'],
                $row['quantity'],
                $row['responsible_user_id'],
                $row['temporary_responsible_user_id'],
                $row['consumable_type_id']
            );
        }
        return $consumables;
    }

    public static function add(Consumable $c): bool {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO Consumable (name, description, arrival_date, image, quantity, responsible_user_id, temporary_responsible_user_id, consumable_type_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $c->name, $c->description, $c->arrival_date, $c->image, $c->quantity,
            $c->responsible_user_id, $c->temporary_responsible_user_id, $c->consumable_type_id
        ]);
    }

    public static function update(Consumable $c): bool {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE Consumable SET name=?, description=?, arrival_date=?, image=?, quantity=?, responsible_user_id=?, temporary_responsible_user_id=?, consumable_type_id=? WHERE id=?");
        return $stmt->execute([
            $c->name, $c->description, $c->arrival_date, $c->image, $c->quantity,
            $c->responsible_user_id, $c->temporary_responsible_user_id, $c->consumable_type_id, $c->id
        ]);
    }

    public static function delete(int $id): bool {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM Consumable WHERE id=?");
        return $stmt->execute([$id]);
    }
}
?>
