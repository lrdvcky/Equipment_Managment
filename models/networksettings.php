<?php
class NetworkSettings {
    public int $id;
    public string $ip_address;
    public ?string $subnet_mask;
    public ?string $gateway;
    public ?string $dns_servers;
    public ?int $equipment_id;

    public function __construct($id, $ip_address, $subnet_mask, $gateway, $dns_servers, $equipment_id) {
        $this->id = $id;
        $this->ip_address = $ip_address;
        $this->subnet_mask = $subnet_mask;
        $this->gateway = $gateway;
        $this->dns_servers = $dns_servers;
        $this->equipment_id = $equipment_id;
    }
}
?>
