<?php
require_once '../connection.php';
require_once '../models/Software.php';
require_once '../models/SoftwareContext.php';

class SoftwareController {

    public static function index(): array {
        return SoftwareContext::getAll();
    }

    public static function store(array $data): bool {
        $software = new Software(
            0,
            $data['name'],
            $data['version'] ?? null,
            $data['developer_name'] ?? null
        );
        return SoftwareContext::add($software);
    }

    public static function update(int $id, array $data): bool {
        $software = new Software(
            $id,
            $data['name'],
            $data['version'] ?? null,
            $data['developer_name'] ?? null
        );
        return SoftwareContext::update($software);
    }

    public static function destroy(int $id): bool {
        return SoftwareContext::delete($id);
    }
}
?>
