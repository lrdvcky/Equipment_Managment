<?php
require_once '../connection.php';
require_once '../models/NetworkSettings.php';
require_once '../models/NetworkSettingsContext.php';

class NetworkSettingsController {

    public static function index(): array {
        return NetworkSettingsContext::getAll();
    }
}

header('Content-Type: application/json; charset=utf-8');
try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['action'] ?? '') === 'get') {
        $pdo   = OpenConnection();
        $data  = NetworkSettingsController::index();
        $out   = [];

        foreach ($data as $ns) {
            $row = get_object_vars($ns);

            $stmt = $pdo->prepare("SELECT name FROM `Equipment` WHERE id = ?");
            $stmt->execute([$row['equipment_id']]);
            $row['equipment_name'] = $stmt->fetchColumn() ?: '';

            $out[] = $row;
        }

        echo json_encode($out, JSON_UNESCAPED_UNICODE);
        exit;
    }
    http_response_code(400);
    echo json_encode(['status'=>'error','message'=>'Invalid request'], JSON_UNESCAPED_UNICODE);
    exit;
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['status'=>'error','message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}

?>
