<?php
// controllers/EquipmentController.php
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../connection.php';
require_once __DIR__ . '/../models/Equipment.php';         // модель оставляем без изменений
require_once __DIR__ . '/../models/EquipmentContext.php';  // контекст тоже

class EquipmentController {
    public static function index(): array {
        return EquipmentContext::getAll();                 // :contentReference[oaicite:0]{index=0}
    }
    public static function store(array $data): bool { /* ... */ }
    public static function update(int $id, array $data): bool { /* ... */ }
    public static function destroy(int $id): bool { /* ... */ }
}

// Обработка AJAX-запроса
if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['action'] ?? '') === 'get') {
    $list = EquipmentController::index();                  // :contentReference[oaicite:1]{index=1}
    // превращаем объекты в ассоц. массивы
    $out  = array_map(fn($e) => get_object_vars($e), $list);
    echo json_encode($out, JSON_UNESCAPED_UNICODE);
    exit;
}

// В остальных случаях — HTTP 400
http_response_code(400);
echo json_encode(['status'=>'error','message'=>'Invalid request'], JSON_UNESCAPED_UNICODE);
exit;
?>