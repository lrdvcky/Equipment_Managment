<?php
class InventoryCheck {
    public ?int $id;
    public string $name;
    public ?string $start_date;
    public ?string $end_date;

    public function __construct(?int $id, string $name, ?string $start_date = null, ?string $end_date = null) {
        $this->id = $id;
        $this->name = $name;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }
}
?>
