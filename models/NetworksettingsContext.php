<?php
require_once 'networksettings.php';
require_once '../connection.php';

class NetworkSettingsContext {
    public static function getAll(): array {
        $settings = [];
        $conn = OpenConnection();
        $sql = "SELECT * FROM NetworkSettings";
        $result = $conn->query($sql);

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $settings[] = new NetworkSettings(
                $row['id'],
                $row['ip_address'],
                $row['subnet_mask'],
                $row['gateway'],
                $row['dns_servers'],
                $row['equipment_id']
            );
        }

        return $settings;
    }

    public static function add(NetworkSettings $ns): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("INSERT INTO NetworkSettings (ip_address, subnet_mask, gateway, dns_servers, equipment_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $ns->ip_address,
            $ns->subnet_mask,
            $ns->gateway,
            $ns->dns_servers,
            $ns->equipment_id
        ]);
    }

    public static function update(NetworkSettings $ns): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("UPDATE NetworkSettings SET ip_address = ?, subnet_mask = ?, gateway = ?, dns_servers = ?, equipment_id = ? WHERE id = ?");
        $stmt->execute([
            $ns->ip_address,
            $ns->subnet_mask,
            $ns->gateway,
            $ns->dns_servers,
            $ns->equipment_id,
            $ns->id
        ]);
    }

    public static function delete(int $id): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("DELETE FROM NetworkSettings WHERE id = ?");
        $stmt->execute([$id]);
    }
}
?>
