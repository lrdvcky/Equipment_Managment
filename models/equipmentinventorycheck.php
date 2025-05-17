<?php
class EquipmentInventoryCheck {
    public int $equipment_id;
    public int $inventory_check_id;
    public ?int $checked_by_user_id;
    public ?string $comment;
    public bool $check;
}
?>
