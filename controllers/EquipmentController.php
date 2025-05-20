<?php
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../connection.php';
require_once __DIR__ . '/../models/Equipment.php';         
require_once __DIR__ . '/../models/EquipmentContext.php';  

class EquipmentController {
    public static function index(): array {
        return EquipmentContext::getAll();                 
    }
    public static function store(array $data): bool { /* ... */ }
    public static function update(int $id, array $data): bool { /* ... */ }
    public static function destroy(int $id): bool { /* ... */ }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['action'] ?? '') === 'get') {
    $list = EquipmentController::index();             
    $out  = array_map(fn($e) => get_object_vars($e), $list);
    echo json_encode($out, JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code(400);
echo json_encode(['status'=>'error','message'=>'Invalid request'], JSON_UNESCAPED_UNICODE);
exit;
?>