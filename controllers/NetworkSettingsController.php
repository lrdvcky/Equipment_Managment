<?php
// Включаем ошибки для отладки — можно убрать, когда всё заработает
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../connection.php';
require_once __DIR__ . '/../models/networksettings.php';
require_once __DIR__ . '/../models/NetworksettingsContext.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $method = $_SERVER['REQUEST_METHOD'];

    // ==== READ: настройки ====
    if ($method === 'GET' && ($_GET['action'] ?? '') === 'get') {
        $pdo  = OpenConnection();
        $list = NetworksettingsContext::getAll();
        $out  = [];
        foreach ($list as $ns) {
            $row = get_object_vars($ns);
            $stmt = $pdo->prepare("SELECT name FROM Equipment WHERE id = ?");
            $stmt->execute([$row['equipment_id']]);
            $row['equipment_name'] = $stmt->fetchColumn() ?: '';
            $out[] = $row;
        }
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ==== READ: оборудование для <select> ====
    if ($method === 'GET' && ($_GET['action'] ?? '') === 'equipment') {
        $pdo = OpenConnection();
        $eqs = $pdo->query("SELECT id,name FROM Equipment")->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($eqs, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ==== CREATE / UPDATE / DELETE ====
    if ($method === 'POST') {
        $input  = json_decode(file_get_contents('php://input'), true) ?: [];
        $action = $input['action'] ?? '';

        switch ($action) {
    case 'create':
        // теперь порядок: id, ip, equipment_id, subnet, gateway, dns
        $ns = new NetworkSettings(
            null,
            $input['data']['ip_address'], 
            $input['data']['equipment_id'],
            $input['data']['subnet_mask']  ?? null,
            $input['data']['gateway']      ?? null,
            $input['data']['dns_servers']  ?? null
        );
        NetworksettingsContext::add($ns);
        echo json_encode(['status'=>'success'], JSON_UNESCAPED_UNICODE);
        break;

    case 'update':
        $ns = new NetworkSettings(
            (int)$input['id'],
            $input['data']['ip_address'],
            (int)$input['data']['equipment_id'],
            $input['data']['subnet_mask']  ?? null,
            $input['data']['gateway']      ?? null,
            $input['data']['dns_servers']  ?? null
        );
        NetworksettingsContext::update($ns);
        echo json_encode(['status'=>'success'], JSON_UNESCAPED_UNICODE);
        break;

            case 'delete':
                NetworksettingsContext::delete((int)$input['id']);
                echo json_encode(['status'=>'success'], JSON_UNESCAPED_UNICODE);
                break;

            default:
                throw new Exception("Unknown action '{$action}'");
        }

        exit;
    }

    // Ловим всё остальное
    http_response_code(400);
    echo json_encode(['status'=>'error','message'=>'Invalid request'], JSON_UNESCAPED_UNICODE);
    exit;

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['status'=>'error','message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}
