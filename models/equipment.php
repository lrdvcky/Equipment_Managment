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
}

// Пример данных
$equipmentList = [];

$item1 = new Equipment();
$item1->id = 1;
$item1->name = "ПК Lenovo";
$item1->photo = null;
$item1->inventory_number = "INV002";
$item1->room_id = 2;
$item1->responsible_user_id = 2;
$item1->temporary_responsible_user_id = 3;
$item1->price = 35000.00;
$item1->model_id = 2;
$item1->comment = "Рабочий компьютер преподавателя";
$item1->direction_name = "Программирование";
$item1->status = "В эксплуатации";

$equipmentList[] = $item1;
