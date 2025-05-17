<?php
class Model {
    public int $id;
    public string $name;
    public ?string $equipment_type;

    public function __construct($id, $name, $equipment_type) {
        $this->id = $id;
        $this->name = $name;
        $this->equipment_type = $equipment_type;
    }
}
?>
