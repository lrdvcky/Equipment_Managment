<?php
// файл: models/networksettings.php

class NetworkSettings {
    public ?int $id;
    public string $ip_address;
    public int $equipment_id;
    public ?string $subnet_mask;
    public ?string $gateway;
    public ?string $dns_servers;

    public function __construct(
        ?int $id,
        string $ip_address,
        int $equipment_id,
        ?string $subnet_mask = null,
        ?string $gateway = null,
        ?string $dns_servers = null
    ) {
        $this->id           = $id;
        $this->ip_address   = $ip_address;
        $this->equipment_id = $equipment_id;
        $this->subnet_mask  = $subnet_mask;
        $this->gateway      = $gateway;
        $this->dns_servers  = $dns_servers;
    }
}
