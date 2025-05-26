<?php
require_once __DIR__ . '/../connection.php';
require_once __DIR__ . '/Equipment.php';

class EquipmentContext {

    /** @return Equipment[] */
    public static function getAll(): array {
        $conn = OpenConnection();
        $sql = "
            SELECT e.*, r.name AS room_name,
                   u1.last_name AS resp_last, u1.first_name AS resp_first, u1.middle_name AS resp_middle,
                   u2.last_name AS temp_last, u2.first_name AS temp_first, u2.middle_name AS temp_middle
            FROM Equipment e
            LEFT JOIN Room r ON e.room_id = r.id
            LEFT JOIN User u1 ON e.responsible_user_id = u1.id
            LEFT JOIN User u2 ON e.temporary_responsible_user_id = u2.id";
        $stmt = $conn->query($sql);
        $out = [];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $mime = null; $photoData = null;
            if (!empty($row['photo'])) {
                $mime = $finfo->buffer($row['photo']) ?: 'image/jpeg';
                $photoData = 'data:' . $mime . ';base64,' . base64_encode($row['photo']);
            }
            $respName = trim("{$row['resp_last']} {$row['resp_first']} {$row['resp_middle']}");
            $tempName = trim("{$row['temp_last']} {$row['temp_first']} {$row['temp_middle']}");

            $out[] = new Equipment(
                (int)$row['id'],
                $row['name'],
                $photoData,
                $row['inventory_number'],
                $row['room_id'] ? (int)$row['room_id'] : null,
                $row['room_name'],
                $row['responsible_user_id'] ? (int)$row['responsible_user_id'] : null,
                $respName ?: null,
                $row['temporary_responsible_user_id'] ? (int)$row['temporary_responsible_user_id'] : null,
                $tempName ?: null,
                $row['price'] !== null ? (float)$row['price'] : null,
                null, // model_id устарел
                $row['model_name'] ?? null,
                $row['comment'],
                $row['direction_name'],
                $row['status'],
                $row['equipment_type'],
                $row['inventory_section']
            );
        }
        return $out;
    }

    public static function getById(int $id) {
        $conn = OpenConnection();
        $stmt = $conn->prepare('SELECT * FROM Equipment WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }

    public static function create(array $d): int {
        $conn = OpenConnection();
        $stmt = $conn->prepare('INSERT INTO Equipment (name, inventory_number, room_id, responsible_user_id, temporary_responsible_user_id, price, model_name, direction_name, status, comment, equipment_type, photo, inventory_section) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute([
            $d['name'],$d['inventory_number'],$d['room_id'],$d['responsible_user_id'],$d['temporary_responsible_user_id'],$d['price'],$d['model_name'],$d['direction_name'],$d['status'],$d['comment'],$d['equipment_type'],$d['photo'],$d['inventory_section']
        ]);
        return (int)$conn->lastInsertId();
    }

    public static function update(int $id, array $d): void {
        $conn = OpenConnection();
        $sql = 'UPDATE Equipment SET name=?, inventory_number=?, room_id=?, responsible_user_id=?, temporary_responsible_user_id=?, price=?, model_name=?, direction_name=?, status=?, comment=?, equipment_type=?, inventory_section=?';
        $params = [$d['name'],$d['inventory_number'],$d['room_id'],$d['responsible_user_id'],$d['temporary_responsible_user_id'],$d['price'],$d['model_name'],$d['direction_name'],$d['status'],$d['comment'],$d['equipment_type'],$d['inventory_section']];
        if (array_key_exists('photo', $d)) { $sql .= ', photo=?'; $params[] = $d['photo']; }
        $sql .= ' WHERE id=?';
        $params[] = $id;
        $conn->prepare($sql)->execute($params);
    }

    public static function delete(int $id): void {
        OpenConnection()->prepare('DELETE FROM Equipment WHERE id=?')->execute([$id]);
    }
    /**
     * Проверяет, есть ли оборудование с таким инвентарным номером
     * @param int $inv
     * @return bool
     */
    public static function existsInventoryNumber(int $inv): bool
    {
        $conn = OpenConnection();
        $stmt = $conn->prepare(
            'SELECT COUNT(*) FROM Equipment WHERE inventory_number = ?'
        );
        $stmt->execute([$inv]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
