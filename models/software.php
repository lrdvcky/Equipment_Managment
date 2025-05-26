<?php
// models/Software.php

class Software {
    public int    $id;
    public string $name;
    public ?string $version;
    public ?string $developer_name;
    public array  $equipment;         // <--- новое свойство

    /**
     * @param int $id
     * @param string $name
     * @param string|null $version
     * @param string|null $developer_name
     * @param array $equipment — массив имён оборудования
     */
    public function __construct($id, $name, $version, $developer_name, $equipment = []) {
        $this->id             = $id;
        $this->name           = $name;
        $this->version        = $version;
        $this->developer_name = $developer_name;
        $this->equipment      = $equipment;
    }
}
