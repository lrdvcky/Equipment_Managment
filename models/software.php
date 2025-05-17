<?php
class Software {
    public int $id;
    public string $name;
    public ?string $version;
    public ?string $developer_name;

    public function __construct($id, $name, $version, $developer_name) {
        $this->id = $id;
        $this->name = $name;
        $this->version = $version;
        $this->developer_name = $developer_name;
    }
}
?>
