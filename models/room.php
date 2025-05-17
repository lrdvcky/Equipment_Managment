<?php
class Room {
    public int $id;
    public string $name;
    public ?string $short_name;
    public ?int $responsible_user_id;
    public ?int $temporary_responsible_user_id;

    public function __construct($id, $name, $short_name, $responsible_user_id, $temporary_responsible_user_id) {
        $this->id = $id;
        $this->name = $name;
        $this->short_name = $short_name;
        $this->responsible_user_id = $responsible_user_id;
        $this->temporary_responsible_user_id = $temporary_responsible_user_id;
    }
}
?>
