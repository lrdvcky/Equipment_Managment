<?php
require_once '../connection.php';
require_once '../models/EquipmentInventoryCheck.php';
require_once '../models/EquipmentInventoryCheckContext.php';

class EquipmentInventoryCheckController {

    public static function index(): array {
        return EquipmentInventoryCheckContext::getAll();
    }

    public static function store(array $data): bool {
        $check = new EquipmentInventoryCheck(
            $data['equipment_id'],
            $data['inventory_check_id'],
            $data['checked_by_user_id'] ?? null,
            $data['comment'] ?? null,
            $data['check']
        );
        return EquipmentInventoryCheckContext::add($check);
    }

    public static function update(array $data): bool {
        $check = new EquipmentInventoryCheck(
            $data['equipment_id'],
            $data['inventory_check_id'],
            $data['checked_by_user_id'] ?? null,
            $data['comment'] ?? null,
            $data['check']
        );
        return EquipmentInventoryCheckContext::update($check);
    }

    public static function destroy(int $equipment_id, int $inventory_check_id): bool {
        return EquipmentInventoryCheckContext::delete($equipment_id, $inventory_check_id);
    }
}
?>
