<?php
require_once '../connection.php';
require_once '../models/Model.php';
require_once '../models/ModelContext.php';

class ModelController {

    public static function index(): array {
        return ModelContext::getAll();
    }

    public static function store(array $data): bool {
        $model = new Model(
            0,
            $data['name'],
            $data['equipment_type'] ?? null
        );
        return ModelContext::add($model);
    }

    public static function update(int $id, array $data): bool {
        $model = new Model(
            $id,
            $data['name'],
            $data['equipment_type'] ?? null
        );
        return ModelContext::update($model);
    }

    public static function destroy(int $id): bool {
        return ModelContext::delete($id);
    }
}
?>
