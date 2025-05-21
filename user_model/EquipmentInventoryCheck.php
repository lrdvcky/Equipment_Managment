<?php
class EquipmentInventoryCheck {
    public int $equipment_id;
    public int $inventory_check_id;
    public ?int $checked_by_user_id;
    public ?string $comment;
    public bool $check;

    public function __construct($equipment_id, $inventory_check_id, $checked_by_user_id, $comment, $check) {
        $this->equipment_id = $equipment_id;
        $this->inventory_check_id = $inventory_check_id;
        $this->checked_by_user_id = $checked_by_user_id;
        $this->comment = $comment;
        $this->check = $check;
    }
}
?>
