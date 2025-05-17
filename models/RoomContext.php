<?php
require_once 'Room.php';
require_once '../connection.php';

class RoomContext {
    public static function getAll(): array {
        $rooms = [];
        $conn = OpenConnection();
        $sql = "SELECT * FROM Room";
        $result = $conn->query($sql);

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $rooms[] = new Room(
                $row['id'],
                $row['name'],
                $row['short_name'],
                $row['responsible_user_id'],
                $row['temporary_responsible_user_id']
            );
        }

        return $rooms;
    }

    public static function add(Room $room): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("INSERT INTO Room (name, short_name, responsible_user_id, temporary_responsible_user_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $room->name,
            $room->short_name,
            $room->responsible_user_id,
            $room->temporary_responsible_user_id
        ]);
    }

    public static function update(Room $room): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("UPDATE Room SET name = ?, short_name = ?, responsible_user_id = ?, temporary_responsible_user_id = ? WHERE id = ?");
        $stmt->execute([
            $room->name,
            $room->short_name,
            $room->responsible_user_id,
            $room->temporary_responsible_user_id,
            $room->id
        ]);
    }

    public static function delete(int $id): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("DELETE FROM Room WHERE id = ?");
        $stmt->execute([$id]);
    }
}
?>
