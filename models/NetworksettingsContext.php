<?php
require_once __DIR__ . '/networksettings.php';
require_once __DIR__ . '/../connection.php';

class NetworksettingsContext {
    public static function getAll(): array {
        $pdo = OpenConnection();
        $stmt = $pdo->query("SELECT * FROM NetworkSettings");
        $out = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $out[] = new NetworkSettings(
                (int)$row['id'],
                $row['ip_address'],
                (int)$row['equipment_id'],
                $row['subnet_mask'],
                $row['gateway'],
                $row['dns_servers']
            );
        }
        return $out;
    }

    public static function getByEquipment(int $equipment_id): ?NetworkSettings {
        $pdo  = OpenConnection();
        $stmt = $pdo->prepare("SELECT * FROM NetworkSettings WHERE equipment_id = ?");
        $stmt->execute([$equipment_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return new NetworkSettings(
            (int)$row['id'],
            $row['ip_address'],
            (int)$row['equipment_id'],
            $row['subnet_mask'],
            $row['gateway'],
            $row['dns_servers']
        );
    }

    public static function add(NetworkSettings $ns): int {
        $pdo = OpenConnection();
        $sql = "INSERT INTO NetworkSettings 
                  (ip_address, subnet_mask, gateway, dns_servers, equipment_id)
                VALUES
                  (:ip, :mask, :gw, :dns, :eid)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':ip'   => $ns->ip_address,
            ':mask' => $ns->subnet_mask,
            ':gw'   => $ns->gateway,
            ':dns'  => $ns->dns_servers,
            ':eid'  => $ns->equipment_id,
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(NetworkSettings $ns): void {
        $pdo = OpenConnection();
        $sql = "UPDATE NetworkSettings SET
                  ip_address   = :ip,
                  subnet_mask  = :mask,
                  gateway      = :gw,
                  dns_servers  = :dns,
                  equipment_id = :eid
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':ip'   => $ns->ip_address,
            ':mask' => $ns->subnet_mask,
            ':gw'   => $ns->gateway,
            ':dns'  => $ns->dns_servers,
            ':eid'  => $ns->equipment_id,
            ':id'   => $ns->id,
        ]);
    }

    public static function delete(int $id): void {
        $pdo = OpenConnection();
        $pdo->prepare("DELETE FROM NetworkSettings WHERE id = ?")
            ->execute([$id]);
    }
}
?>
