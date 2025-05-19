<?php
require_once '../connection.php';
require_once '../models/ConsumableProperty.php';
require_once '../models/ConsumablePropertyContext.php';

class ConsumablePropertyController {

    public static function index(): array {
        return ConsumablePropertyContext::getAll();
    }

    public static function store(array $data): bool {
        $property = new ConsumableProperty(
            0,
            $data['consumable_id'],
            $data['property_name'],
            $data['property_value']
        );
        return ConsumablePropertyContext::add($property);
    }

    public static function update(int $id, array $data): bool {
        $property = new ConsumableProperty(
            $id,
            $data['consumable_id'],
            $data['property_name'],
            $data['property_value']
        );
        return ConsumablePropertyContext::update($property);
    }

    public static function destroy(int $id): bool {
        return ConsumablePropertyContext::delete($id);
    }
}
?>
