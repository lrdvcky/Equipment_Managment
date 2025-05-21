<?php 
class Equipment {
    public int $id;
    public string $name;
    public string $inventory_number;
    public ?string $room_name;
    public ?string $comment;

    public function __construct($id, $name, $inventory_number, $room_name, $comment) {
        $this->id = $id;
        $this->name = $name;
        $this->inventory_number = $inventory_number;
        $this->room_name = $room_name;
        $this->comment = $comment;
    }
}

?>