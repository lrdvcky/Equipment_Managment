<?php
class Equipment {
    public int $id;
    public string $name;
    public ?string $photo;
    public string $inventory_number;
    public ?int $room_id;
    public ?string $room_name;
    public ?int $responsible_user_id;
    public ?string $responsible_user_name;
    public ?int $temporary_responsible_user_id;
    public ?string $temporary_responsible_user_name;
    public ?float $price;
    public ?int $model_id;
    public ?string $model_name;
    public ?string $comment;
    public ?string $direction_name;
    public ?string $status;
    public ?string $equipment_type;

    // ✅ Новое свойство для раздела
    public ?string $inventory_section;

    public function __construct(
        int     $id,
        string  $name,
        ?string $photo,
        string  $inventory_number,
        ?int    $room_id,
        ?string $room_name,
        ?int    $responsible_user_id,
        ?string $responsible_user_name,
        ?int    $temporary_responsible_user_id,
        ?string $temporary_responsible_user_name,
        ?float  $price,
        ?int    $model_id,
        ?string $model_name,
        ?string $comment,
        ?string $direction_name,
        ?string $status,
        ?string $equipment_type,
        ?string $inventory_section   // ✅ параметр в конце
    ) {
        $this->id                            = $id;
        $this->name                          = $name;
        $this->photo                         = $photo;
        $this->inventory_number              = $inventory_number;
        $this->room_id                       = $room_id;
        $this->room_name                     = $room_name;
        $this->responsible_user_id           = $responsible_user_id;
        $this->responsible_user_name         = $responsible_user_name;
        $this->temporary_responsible_user_id = $temporary_responsible_user_id;
        $this->temporary_responsible_user_name = $temporary_responsible_user_name;
        $this->price                         = $price;
        $this->model_id                      = $model_id;
        $this->model_name                    = $model_name;
        $this->comment                       = $comment;
        $this->direction_name                = $direction_name;
        $this->status                        = $status;
        $this->equipment_type                = $equipment_type;
        $this->inventory_section             = $inventory_section; // ✅ присваиваем
    }
}
