<?php
class Equipment {
    public int $id;
    public string $name;
    public ?string $photo;
    public string $inventory_number;
    public ?int $room_id;
    public ?int $responsible_user_id;
    public ?int $temporary_responsible_user_id;
    public ?float $price;
    public ?int $model_id;
    public ?string $comment;
    public ?string $direction_name;
    public ?string $status;

    public function __construct(
        int $id,
        string $name,
        ?string $photo,
        string $inventory_number,
        ?int $room_id,
        ?int $responsible_user_id,
        ?int $temporary_responsible_user_id,
        ?float $price,
        ?int $model_id,
        ?string $comment,
        ?string $direction_name,
        ?string $status
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->photo = $photo;
        $this->inventory_number = $inventory_number;
        $this->room_id = $room_id;
        $this->responsible_user_id = $responsible_user_id;
        $this->temporary_responsible_user_id = $temporary_responsible_user_id;
        $this->price = $price;
        $this->model_id = $model_id;
        $this->comment = $comment;
        $this->direction_name = $direction_name;
        $this->status = $status;
    }
}
?>
