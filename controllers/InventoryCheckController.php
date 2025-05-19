<?php
require_once '../connection.php';
require_once '../models/InventoryCheck.php';
require_once '../models/InventoryCheckContext.php';

class InventoryCheckController {

    public static function index(): array {
        return InventoryCheckContext::getAll();
    }

    public static function store(array $data): bool {
        $inventory = new InventoryCheck(
            0,
            $data['name'],
            $data['start_date'] ?? null,
            $data['end_date'] ?? null
        );
        return InventoryCheckContext::add($inventory);
    }

    public static function update(int $id, array $data): bool {
        $inventory = new InventoryCheck(
            $id,
            $data['name'],
            $data['start_date'] ?? null,
            $data['end_date'] ?? null
        );
        return InventoryCheckContext::update($inventory);
    }

    public static function destroy(int $id): bool {
        return InventoryCheckContext::delete($id);
    }
}
?>
