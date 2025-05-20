<?php
require_once __DIR__ . '/consumable.php';
require_once __DIR__ . '/../connection.php';

class ConsumableContext {
    /** @return Consumable[] */
    public static function getAll(): array {
        $pdo  = OpenConnection();
        $stmt = $pdo->query("SELECT * FROM `Consumable`");
        $list = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $list[] = new Consumable(
                $row['id'],                // int
                $row['name'],              // string
                $row['description'],       // string
                $row['arrival_date'],      // string
                $row['image'] ?? null,     // blob/filepath
                (int)$row['quantity'],     // int
                (int)$row['responsible_user_id'],          // int
                (int)$row['temporary_responsible_user_id'],// int
                (int)$row['consumable_type_id']            // int
            );
        }
        return $list;
    }

    /** Создать новый расходник */
    public static function add(Consumable $c): bool {
        $pdo = OpenConnection();
        $stmt = $pdo->prepare(
            "INSERT INTO `Consumable` \
            (name, description, arrival_date, image, quantity, responsible_user_id, temporary_responsible_user_id, consumable_type_id) \
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $c->name,
            $c->description,
            $c->arrival_date,
            $c->image,
            $c->quantity,
            $c->responsible_user_id,
            $c->temporary_responsible_user_id,
            $c->consumable_type_id
        ]);
    }

    /** Обновить существующий расходник */
    public static function update(Consumable $c): bool {
        $pdo = OpenConnection();
        $stmt = $pdo->prepare(
            "UPDATE `Consumable` SET \
            name = ?, description = ?, arrival_date = ?, image = ?, quantity = ?, \
            responsible_user_id = ?, temporary_responsible_user_id = ?, consumable_type_id = ? \
            WHERE id = ?"
        );
        return $stmt->execute([
            $c->name,
            $c->description,
            $c->arrival_date,
            $c->image,
            $c->quantity,
            $c->responsible_user_id,
            $c->temporary_responsible_user_id,
            $c->consumable_type_id,
            $c->id
        ]);
    }

    /** Удалить расходник по ID */
    public static function delete(int $id): bool {
        $pdo = OpenConnection();
        $stmt = $pdo->prepare("DELETE FROM `Consumable` WHERE id = ?");
        return $stmt->execute([$id]);
    }
}