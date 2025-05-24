<?php
declare(strict_types=1);

class Consumable
{
    // Сделали свойство nullable
    public ?int $id;
    public string $name;
    public ?string $description;
    public ?string $arrival_date;
    public $image;  // оставляем как есть
    public int $quantity;
    public int $responsible_user_id;
    public ?int $temporary_responsible_user_id;
    public int $consumable_type_id;

    /**
     * @param int|null $id — при создании передают null, при чтении из БД — int
     */
    public function __construct(
        ?int $id,
        string $name,
        ?string $description,
        ?string $arrival_date,
        $image,
        int $quantity,
        int $responsible_user_id,
        ?int $temporary_responsible_user_id,
        int $consumable_type_id
    ) {
        $this->id                           = $id;
        $this->name                         = $name;
        $this->description                  = $description;
        $this->arrival_date                 = $arrival_date;
        $this->image                        = $image;
        $this->quantity                     = $quantity;
        $this->responsible_user_id          = $responsible_user_id;
        $this->temporary_responsible_user_id= $temporary_responsible_user_id;
        $this->consumable_type_id           = $consumable_type_id;
    }
}
