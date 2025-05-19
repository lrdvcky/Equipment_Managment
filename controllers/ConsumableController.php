<?php
require_once '../connection.php';
require_once '../models/Consumable.php';
require_once '../models/ConsumableContext.php';

class ConsumableController {

    public static function index(): array {
        return ConsumableContext::getAll();
    }

    public static function store(array $data): bool {
        $consumable = new Consumable(
            0,
            $data['name'],
            $data['description'] ?? null,
            $data['arrival_date'] ?? null,
            $data['image'] ?? null,
            $data['quantity'] ?? null,
            $data['responsible_user_id'] ?? null,
            $data['temporary_responsible_user_id'] ?? null,
            $data['consumable_type_id'] ?? null
        );
        return ConsumableContext::add($consumable);
    }

    public static function update(int $id, array $data): bool {
        $consumable = new Consumable(
            $id,
            $data['name'],
            $data['description'] ?? null,
            $data['arrival_date'] ?? null,
            $data['image'] ?? null,
            $data['quantity'] ?? null,
            $data['responsible_user_id'] ?? null,
            $data['temporary_responsible_user_id'] ?? null,
            $data['consumable_type_id'] ?? null
        );
        return ConsumableContext::update($consumable);
    }

    public static function destroy(int $id): bool {
        return ConsumableContext::delete($id);
    }
}
?>
