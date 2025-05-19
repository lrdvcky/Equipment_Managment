<?php
require_once '../connection.php';
require_once '../models/EquipmentSoftware.php';
require_once '../models/EquipmentSoftwareContext.php';

class EquipmentSoftwareController {

    public static function index(): array {
        return EquipmentSoftwareContext::getAll();
    }

    public static function store(array $data): bool {
        $relation = new EquipmentSoftware(
            $data['equipment_id'],
            $data['software_id']
        );
        return EquipmentSoftwareContext::add($relation);
    }

        $old = new EquipmentSoftware($oldData['equipment_id'], $oldData['software_id']);
        $new = new EquipmentSoftware($newData['equipment_id'], $newData['software_id']);
        return EquipmentSoftwareContext::update($old, $new);
    }

    public static function destroy(int $equipment_id, int $software_id): bool {
        return EquipmentSoftwareContext::delete($equipment_id, $software_id);
    }
}
?>
