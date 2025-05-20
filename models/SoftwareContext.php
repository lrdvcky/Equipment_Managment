<?php
// models/SoftwareContext.php

require_once __DIR__ . '/Software.php';
require_once __DIR__ . '/../connection.php';

class SoftwareContext {

    /** @return Software[] */
    public static function getAll(): array {
        $conn = OpenConnection();
        $sql  = "SELECT * FROM `Software`";
        $stmt = $conn->query($sql);
        $list = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $list[] = new Software(
                (int)$row['id'],
                $row['name'],
                $row['version'] ?? null,
                $row['developer_name'] ?? null
            );
        }
        return $list;
    }

    public static function add(Software $software): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("
            INSERT INTO `Software` (name, version, developer_name)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([
            $software->name,
            $software->version,
            $software->developer_name
        ]);
    }

    public static function update(Software $software): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("
            UPDATE `Software`
            SET name = ?, version = ?, developer_name = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $software->name,
            $software->version,
            $software->developer_name,
            $software->id
        ]);
    }

    public static function delete(int $id): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("DELETE FROM `Software` WHERE id = ?");
        $stmt->execute([$id]);
    }
}
