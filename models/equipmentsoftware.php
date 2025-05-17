<?php
class EquipmentSoftware {
    public int $equipment_id;
    public int $software_id;

    public function __construct($equipment_id, $software_id) {
        $this->equipment_id = $equipment_id;
        $this->software_id = $software_id;
    }
}
?>
