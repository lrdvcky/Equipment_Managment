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
}
?>
