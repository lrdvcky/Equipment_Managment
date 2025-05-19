<?php
require_once '../connection.php';
require_once '../models/Room.php';
require_once '../models/RoomContext.php';

class RoomController {

    public static function index(): array {
        return RoomContext::getAll();
    }

    public static function store(array $data): bool {
        $room = new Room(
            0,
            $data['name'],
            $data['short_name'] ?? null,
            $data['responsible_user_id'] ?? null,
            $data['temporary_responsible_user_id'] ?? null
        );
        return RoomContext::add($room);
    }

    public static function update(int $id, array $data): bool {
        $room = new Room(
            $id,
            $data['name'],
            $data['short_name'] ?? null,
            $data['responsible_user_id'] ?? null,
            $data['temporary_responsible_user_id'] ?? null
        );
        return RoomContext::update($room);
    }

    public static function destroy(int $id): bool {
        return RoomContext::delete($id);
    }
}
?>
