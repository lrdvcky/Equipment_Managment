<?php
require_once '../connection.php';
require_once '../models/ConsumableType.php';
require_once '../models/ConsumableTypeContext.php';

class ConsumableTypeController {

    public static function index(): array {
        return ConsumableTypeContext::getAll();
    }

    public static function store(array $data): bool {
        $type = new ConsumableType(
            0,
            $data['name']
        );
        return ConsumableTypeContext::add($type);
    }

    public static function update(int $id, array $data): bool {
        $type = new ConsumableType(
            $id,
            $data['name']
        );
        return ConsumableTypeContext::update($type);
    }

    public static function destroy(int $id): bool {
        return ConsumableTypeContext::delete($id);
    }
}
?>
