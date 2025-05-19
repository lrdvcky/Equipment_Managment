<?php
require_once '../connection.php';
require_once '../models/Equipment.php';
require_once '../models/EquipmentContext.php';

class EquipmentController {

    public static function index(): array {
        return EquipmentContext::getAll();
    }

    public static function store(array $data): bool {
        $equipment = new Equipment(
            0,
            $data['name'],
            $data['photo'] ?? null,
            $data['inventory_number'],
            $data['room_id'] ?? null,
            $data['responsible_user_id'] ?? null,
            $data['temporary_responsible_user_id'] ?? null,
            $data['price'] ?? null,
            $data['model_id'] ?? null,
            $data['comment'] ?? null,
            $data['direction_name'] ?? null,
            $data['status'] ?? null
        );
        return EquipmentContext::add($equipment);
    }

    public static function update(int $id, array $data): bool {
        $equipment = new Equipment(
            $id,
            $data['name'],
            $data['photo'] ?? null,
            $data['inventory_number'],
            $data['room_id'] ?? null,
            $data['responsible_user_id'] ?? null,
            $data['temporary_responsible_user_id'] ?? null,
            $data['price'] ?? null,
            $data['model_id'] ?? null,
            $data['comment'] ?? null,
            $data['direction_name'] ?? null,
            $data['status'] ?? null
        );
        return EquipmentContext::update($equipment);
    }

    public static function destroy(int $id): bool {
        return EquipmentContext::delete($id);
    }
}
?>
