<?php
require_once 'Equipment.php';
require_once '../connection.php';

class EquipmentContext {
    public static function getAll(): array {
        $items = [];
        $conn = OpenConnection();
        $sql = "SELECT * FROM Equipment";
        $result = $conn->query($sql);

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $items[] = new Equipment(
                $row['id'],
                $row['name'],
                $row['photo'],
                $row['inventory_number'],
                $row['room_id'],
                $row['responsible_user_id'],
                $row['temporary_responsible_user_id'],
                $row['price'],
                $row['model_id'],
                $row['comment'],
                $row['direction_name'],
                $row['status']
            );
        }

        return $items;
    }

    public static function add(Equipment $item): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("INSERT INTO Equipment (name, photo, inventory_number, room_id, responsible_user_id, temporary_responsible_user_id, price, model_id, comment, direction_name, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $item->name,
            $item->photo,
            $item->inventory_number,
            $item->room_id,
            $item->responsible_user_id,
            $item->temporary_responsible_user_id,
            $item->price,
            $item->model_id,
            $item->comment,
            $item->direction_name,
            $item->status
        ]);
    }

    public static function update(Equipment $item): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("UPDATE Equipment SET name = ?, photo = ?, inventory_number = ?, room_id = ?, responsible_user_id = ?, temporary_responsible_user_id = ?, price = ?, model_id = ?, comment = ?, direction_name = ?, status = ? WHERE id = ?");
        $stmt->execute([
            $item->name,
            $item->photo,
            $item->inventory_number,
            $item->room_id,
            $item->responsible_user_id,
            $item->temporary_responsible_user_id,
            $item->price,
            $item->model_id,
            $item->comment,
            $item->direction_name,
            $item->status,
            $item->id
        ]);
    }

    public static function delete(int $id): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("DELETE FROM Equipment WHERE id = ?");
        $stmt->execute([$id]);
    }
}
?>
