<?php
require_once 'model.php';
require_once '../connection.php';

class ModelContext {
    public static function getAll(): array {
        $models = [];
        $conn = OpenConnection();
        $sql = "SELECT * FROM Model";
        $result = $conn->query($sql);

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $models[] = new Model(
                $row['id'],
                $row['name'],
                $row['equipment_type']
            );
        }

        return $models;
    }

    public static function add(Model $model): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("INSERT INTO Model (name, equipment_type) VALUES (?, ?)");
        $stmt->execute([
            $model->name,
            $model->equipment_type
        ]);
    }

    public static function update(Model $model): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("UPDATE Model SET name = ?, equipment_type = ? WHERE id = ?");
        $stmt->execute([
            $model->name,
            $model->equipment_type,
            $model->id
        ]);
    }

    public static function delete(int $id): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("DELETE FROM Model WHERE id = ?");
        $stmt->execute([$id]);
    }
}
?>
