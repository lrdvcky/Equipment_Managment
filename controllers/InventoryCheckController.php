<?php
// controllers/InventoryCheckController.php
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../models/InventorycheckContext.php';
require_once __DIR__ . '/../models/inventorycheck.php';
require_once __DIR__ . '/../models/EquipmentInventoryCheckContext.php';
require_once __DIR__ . '/../models/EquipmentInventoryCheck.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Only GET allowed');
    }
    $action = $_GET['action'] ?? '';
    switch ($action) {
        case 'getChecks':
            $items = InventoryCheckContext::getAll();
            $out = array_map(function($i) {
                return [
                    'id'         => $i->id,
                    'name'       => $i->name,
                    'start_date' => $i->start_date,
                    'end_date'   => $i->end_date,
                ];
            }, $items);
            echo json_encode($out, JSON_UNESCAPED_UNICODE);
            exit;

        case 'getResults':
    if (empty($_GET['id']) || !ctype_digit($_GET['id'])) {
        throw new Exception('Missing or invalid id');
    }
    $checkId = (int)$_GET['id'];
    // теперь метод точно существует
    $records = EquipmentInventoryCheckContext::getByCheckId($checkId);
    echo json_encode($records, JSON_UNESCAPED_UNICODE);
    exit;

        case 'addCheck':
            $name = trim($_GET['name'] ?? '');
            if ($name === '') {
                throw new Exception('Name is required');
            }
            $start = $_GET['start_date'] ?: null;
            $end   = $_GET['end_date']   ?: null;
            $item = new InventoryCheck(null, $name, $start, $end);
            InventoryCheckContext::add($item);
            echo json_encode(['status'=>'ok'], JSON_UNESCAPED_UNICODE);
            exit;

        case 'updateCheck':
            if (empty($_GET['id']) || !ctype_digit($_GET['id'])) {
                throw new Exception('Missing or invalid id');
            }
            $id   = (int)$_GET['id'];
            $name = trim($_GET['name'] ?? '');
            if ($name === '') {
                throw new Exception('Name is required');
            }
            $start = $_GET['start_date'] ?: null;
            $end   = $_GET['end_date']   ?: null;
            $item = new InventoryCheck($id, $name, $start, $end);
            InventoryCheckContext::update($item);
            echo json_encode(['status'=>'ok'], JSON_UNESCAPED_UNICODE);
            exit;

        case 'deleteCheck':
            if (empty($_GET['id']) || !ctype_digit($_GET['id'])) {
                throw new Exception('Missing or invalid id');
            }
            InventoryCheckContext::delete((int)$_GET['id']);
            echo json_encode(['status'=>'ok'], JSON_UNESCAPED_UNICODE);
            exit;

        default:
            throw new Exception("Unknown action \"{$action}\"");
    }
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['status'=>'error','message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}
