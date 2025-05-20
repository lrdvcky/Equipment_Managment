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
    $pdo  = OpenConnection();
    $list = EquipmentController::index();
    $out  = [];

    foreach ($list as $e) {
        $row = get_object_vars($e);

        // 1) Аудитория (room)
        $stmt = $pdo->prepare("SELECT name FROM `Room` WHERE id = ?");
        $stmt->execute([$e->room_id]);
        $row['room_name'] = $stmt->fetchColumn() ?: '';

        // 2) Ответственный пользователь
        $stmt = $pdo->prepare("
            SELECT CONCAT_WS(' ', last_name, first_name, middle_name)
            FROM `User` WHERE id = ?
        ");
        $stmt->execute([$e->responsible_user_id]);
        $row['responsible_name'] = $stmt->fetchColumn() ?: '';
        
        if (!empty($e->model_id)) {
            $stmt = $pdo->prepare("SELECT name FROM `Model` WHERE id = ?");
            $stmt->execute([$e->model_id]);
            $row['model_name'] = $stmt->fetchColumn() ?: '';
        } else {
            $row['model_name'] = '';
        }
        // 3) Временно ответственный
        $stmt->execute([$e->temporary_responsible_user_id]);
        $row['temporary_responsible_name'] = $stmt->fetchColumn() ?: '';

        $out[] = $row;
    }

    echo json_encode($out, JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code(400);
echo json_encode(['status'=>'error','message'=>'Invalid request'], JSON_UNESCAPED_UNICODE);
exit;
?>