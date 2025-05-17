<?php
class Consumable {
    public int $id;
    public string $name;
    public ?string $description;
    public ?string $arrival_date;
    public ?string $image;
    public ?int $quantity;
    public ?int $responsible_user_id;
    public ?int $temporary_responsible_user_id;
    public ?int $consumable_type_id;

    public function __construct($id, $name, $description, $arrival_date, $image, $quantity, $responsible_user_id, $temporary_responsible_user_id, $consumable_type_id) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->arrival_date = $arrival_date;
        $this->image = $image;
        $this->quantity = $quantity;
        $this->responsible_user_id = $responsible_user_id;
        $this->temporary_responsible_user_id = $temporary_responsible_user_id;
        $this->consumable_type_id = $consumable_type_id;
    }
}
?>
