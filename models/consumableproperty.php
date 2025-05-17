<?php
class ConsumableProperty {
    public int $id;
    public int $consumable_id;
    public string $property_name;
    public string $property_value;

    public function __construct(int $id, int $consumable_id, string $property_name, string $property_value) {
        $this->id = $id;
        $this->consumable_id = $consumable_id;
        $this->property_name = $property_name;
        $this->property_value = $property_value;
    }
}
?>
