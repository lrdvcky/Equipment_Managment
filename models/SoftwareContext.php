<?php
// models/SoftwareContext.php

require_once __DIR__ . '/Software.php';
require_once __DIR__ . '/../connection.php';

class SoftwareContext {
    /** @return Software[] */
    public static function getAll(): array {
        $conn = OpenConnection();
        $stmt = $conn->query("SELECT * FROM `Software` ORDER BY id");
        $out  = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $out[] = new Software(
                (int)$row['id'],
                $row['name'],
                $row['version'],
                $row['developer_name']
            );
        }
        return $out;
    }

    /** @return int — ID новой записи */
    public static function create(array $d): int {
        $conn = OpenConnection();
        $stmt = $conn->prepare("
            INSERT INTO `Software` (name, version, developer_name)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([
            $d['name'],
            $d['version']        ?? null,
            $d['developer_name'] ?? null
        ]);
        return (int)$conn->lastInsertId();
    }

    public static function update(int $id, array $d): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("
            UPDATE `Software`
               SET name = ?, version = ?, developer_name = ?
             WHERE id = ?
        ");
        $stmt->execute([
            $d['name'],
            $d['version']        ?? null,
            $d['developer_name'] ?? null,
            $id
        ]);
    }

    public static function delete(int $id): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("DELETE FROM `Software` WHERE id = ?");
        try {
            $stmt->execute([$id]);
        } catch (\PDOException $e) {
            // код 23000 — нарушение внешнего ключа
            if ($e->getCode() === '23000') {
                throw new \Exception(
                    "Невозможно удалить программу: она используется в связях с оборудованием"
                );
            }
            throw $e;
        }
    }
}
