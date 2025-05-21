<?php
// models/InventorycheckContext.php
require_once 'inventorycheck.php';
require_once '../connection.php';

class InventoryCheckContext {
    public static function getAll(): array {
        $items = [];
        $conn  = OpenConnection();
        $stmt  = $conn->query("SELECT * FROM InventoryCheck");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $items[] = new InventoryCheck(
                $row['id'],
                $row['name'],
                $row['start_date'],
                $row['end_date']
            );
        }
        return $items;
    }

    public static function add(InventoryCheck $item): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare(
            "INSERT INTO InventoryCheck (name, start_date, end_date) VALUES (?, ?, ?)"
        );
        $stmt->execute([
            $item->name,
            $item->start_date,
            $item->end_date
        ]);
    }

    public static function update(InventoryCheck $item): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare(
            "UPDATE InventoryCheck SET name = ?, start_date = ?, end_date = ? WHERE id = ?"
        );
        $stmt->execute([
            $item->name,
            $item->start_date,
            $item->end_date,
            $item->id
        ]);
    }

    public static function delete(int $id): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("DELETE FROM InventoryCheck WHERE id = ?");
        $stmt->execute([$id]);
    }
}
?>
