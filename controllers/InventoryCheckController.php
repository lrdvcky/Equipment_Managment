<?php
// controllers/InventoryCheckController.php
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../connection.php';
require_once __DIR__ . '/../models/InventoryCheckContext.php';
require_once __DIR__ . '/../models/EquipmentInventoryCheckContext.php';
require_once __DIR__ . '/../models/EquipmentSoftwareContext.php'; // <— Новое

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Only GET allowed');
    }

    $action = $_GET['action'] ?? '';
    switch ($action) {
        case 'getChecks':
            $checks = InventoryCheckContext::getAll();
            echo json_encode(
                array_map(fn($c) => get_object_vars($c), $checks),
                JSON_UNESCAPED_UNICODE
            );
            exit;

        case 'getResults':
            if (empty($_GET['id']) || !ctype_digit($_GET['id'])) {
                throw new Exception('Missing or invalid id');
            }
            $checkId = (int)$_GET['id'];

            $raw = EquipmentInventoryCheckContext::getByCheckId($checkId);

            $out = [];
            foreach ($raw as $row) {
                $softList = EquipmentSoftwareContext::getByEquipmentId($row['equipment_id']);
                $names = array_map(
                    fn($s) => is_object($s) ? $s->name : ($s['name'] ?? ''),
                    $softList
                );
                $row['software'] = implode(', ', $names);
                $out[] = $row;
            }

            echo json_encode($out, JSON_UNESCAPED_UNICODE);
            exit;

        default:
            throw new Exception("Unknown action “{$action}”");
    }
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(
        ['status'=>'error','message'=>$e->getMessage()],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}
