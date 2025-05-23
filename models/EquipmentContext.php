<?php
require_once __DIR__ . '/Equipment.php';
require_once __DIR__ . '/../connection.php';

class EquipmentContext {
    /** @return Equipment[] */
    public static function getAll(): array {
        $conn = OpenConnection();
        $sql = "
          SELECT 
            e.*,
            r.name AS room_name,
            u1.last_name AS resp_last, u1.first_name AS resp_first, u1.middle_name AS resp_middle,
            u2.last_name AS temp_last, u2.first_name AS temp_first, u2.middle_name AS temp_middle,
            m.name AS model_name,
            e.inventory_section
          FROM `Equipment` e
          LEFT JOIN `Room` r ON e.room_id = r.id
          LEFT JOIN `User` u1 ON e.responsible_user_id = u1.id
          LEFT JOIN `User` u2 ON e.temporary_responsible_user_id = u2.id
          LEFT JOIN `Model` m ON e.model_id = m.id
        ";

        $stmt = $conn->query($sql);
        $out = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $photoData = null;
            if (!empty($row['photo'])) {
                $base64 = base64_encode($row['photo']);
                $photoData = "data:image/jpeg;base64,{$base64}";
            }

            $respName = trim("{$row['resp_last']} {$row['resp_first']} {$row['resp_middle']}");
            $tempName = trim("{$row['temp_last']} {$row['temp_first']} {$row['temp_middle']}");

            $out[] = new Equipment(
                (int)$row['id'],
                $row['name'],
                $photoData,
                $row['inventory_number'],
                $row['room_id'] !== null ? (int)$row['room_id'] : null,
                $row['room_name'],
                $row['responsible_user_id'] !== null ? (int)$row['responsible_user_id'] : null,
                $respName ?: null,
                $row['temporary_responsible_user_id'] !== null ? (int)$row['temporary_responsible_user_id'] : null,
                $tempName ?: null,
                $row['price'] !== null ? (float)$row['price'] : null,
                $row['model_id'] !== null ? (int)$row['model_id'] : null,
                $row['model_name'],
                $row['comment'],
                $row['direction_name'],
                $row['status'],
                $row['equipment_type'] ?? null,
                $row['inventory_section'] ?? null
            );
        }

        return $out;
    }

    public static function create(array $data): int {
        $conn = OpenConnection();
        $stmt = $conn->prepare("
            INSERT INTO Equipment (
                name,
                inventory_number,
                room_id,
                responsible_user_id,
                temporary_responsible_user_id,
                price,
                model_id,
                direction_name,
                status,
                comment,
                equipment_type,
                photo,
                inventory_section
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['name'],
            $data['inventory_number'],
            $data['room_id'],
            $data['responsible_user_id'],
            $data['temporary_responsible_user_id'],
            $data['price'],
            $data['model_id'],
            $data['direction_name'],
            $data['status'],
            $data['comment'],
            $data['equipment_type'],
            $data['photo'],
            $data['inventory_section']
        ]);

        return (int)$conn->lastInsertId();
    }

    public static function update(int $id, array $data): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("
            UPDATE Equipment SET 
                name                       = ?,
                inventory_number           = ?,
                room_id                    = ?,
                responsible_user_id        = ?,
                temporary_responsible_user_id = ?,
                price                      = ?,
                model_id                   = ?,
                direction_name             = ?,
                status                     = ?,
                comment                    = ?,
                equipment_type             = ?,
                inventory_section          = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $data['name'],
            $data['inventory_number'],
            $data['room_id'],
            $data['responsible_user_id'],
            $data['temporary_responsible_user_id'],
            $data['price'],
            $data['model_id'],
            $data['direction_name'],
            $data['status'],
            $data['comment'],
            $data['equipment_type'],
            $data['inventory_section'],
            $id
        ]);
    }

    public static function delete(int $id): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("DELETE FROM `Equipment` WHERE id = ?");
        $stmt->execute([$id]);
    }
}
