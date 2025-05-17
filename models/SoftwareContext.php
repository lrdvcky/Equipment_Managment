<?php
require_once 'Software.php';
require_once '../connection.php';

class SoftwareContext {
    public static function getAll(): array {
        $softwares = [];
        $conn = OpenConnection();
        $sql = "SELECT * FROM Software";
        $result = $conn->query($sql);

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $softwares[] = new Software(
                $row['id'],
                $row['name'],
                $row['version'],
                $row['developer_name']
            );
        }

        return $softwares;
    }

    public static function add(Software $software): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("INSERT INTO Software (name, version, developer_name) VALUES (?, ?, ?)");
        $stmt->execute([
            $software->name,
            $software->version,
            $software->developer_name
        ]);
    }

    public static function update(Software $software): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("UPDATE Software SET name = ?, version = ?, developer_name = ? WHERE id = ?");
        $stmt->execute([
            $software->name,
            $software->version,
            $software->developer_name,
            $software->id
        ]);
    }

    public static function delete(int $id): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("DELETE FROM Software WHERE id = ?");
        $stmt->execute([$id]);
    }
}
?>
