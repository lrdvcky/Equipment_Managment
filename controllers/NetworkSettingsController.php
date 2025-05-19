<?php
require_once '../connection.php';
require_once '../models/NetworkSettings.php';
require_once '../models/NetworkSettingsContext.php';

class NetworkSettingsController {

    public static function index(): array {
        return NetworkSettingsContext::getAll();
    }

    public static function store(array $data): bool {
        $settings = new NetworkSettings(
            0,
            $data['ip_address'],
            $data['subnet_mask'] ?? null,
            $data['gateway'] ?? null,
            $data['dns_servers'] ?? null,
            $data['equipment_id'] ?? null
        );
        return NetworkSettingsContext::add($settings);
    }

    public static function update(int $id, array $data): bool {
        $settings = new NetworkSettings(
            $id,
            $data['ip_address'],
            $data['subnet_mask'] ?? null,
            $data['gateway'] ?? null,
            $data['dns_servers'] ?? null,
            $data['equipment_id'] ?? null
        );
        return NetworkSettingsContext::update($settings);
    }

    public static function destroy(int $id): bool {
        return NetworkSettingsContext::delete($id);
    }
}
?>
