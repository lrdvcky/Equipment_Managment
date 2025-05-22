<?php
// Включаем ошибки для отладки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../connection.php';
require_once __DIR__ . '/../models/networksettings.php';
require_once __DIR__ . '/../models/NetworksettingsContext.php';

header('Content-Type: application/json; charset=utf-8');

/**
 * Проверка доступности по TCP (порт 80).
 */
function checkDevice(string $ip, int $port = 80, float $timeout = 0.5): bool {
    $fp = @fsockopen($ip, $port, $errno, $errstr, $timeout);
    if ($fp) {
        fclose($fp);
        return true;
    }
    return false;
}

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_REQUEST['action'] ?? '';

    // ==== READ: все настройки ====
    if ($method === 'GET' && $action === 'get') {
        $pdo  = OpenConnection();
        $list = NetworksettingsContext::getAll();
        $out  = [];
        foreach ($list as $ns) {
            $row = get_object_vars($ns);
            // имя оборудования
            $stmt = $pdo->prepare("SELECT name FROM Equipment WHERE id = ?");
            $stmt->execute([$row['equipment_id']]);
            $row['equipment_name'] = $stmt->fetchColumn() ?: '';
            // статус пока пустой, заполняется при отдельной проверке
            $row['status'] = '';
            $out[] = $row;
        }
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ==== READ: список оборудования ====
    if ($method === 'GET' && $action === 'equipment') {
        $pdo = OpenConnection();
        $eqs = $pdo->query("SELECT id,name FROM Equipment")->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($eqs, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ==== READ: настройки по оборудованию ====
    if ($method === 'GET' && $action === 'getByEquipment') {
        $eqId = isset($_GET['equipment_id']) ? (int)$_GET['equipment_id'] : 0;
        $ns = NetworksettingsContext::getByEquipment($eqId);
        echo json_encode($ns ? get_object_vars($ns) : null, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ==== READ: проверка всех устройств ====
    if ($method === 'GET' && $action === 'check') {
        $pdo  = OpenConnection();
        $list = NetworksettingsContext::getAll();
        $out  = [];
        foreach ($list as $ns) {
            $status = checkDevice($ns->ip_address) ? 'online' : 'offline';
            $out[] = ['id'=>$ns->id, 'status'=>$status];
        }
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ==== CREATE / UPDATE / DELETE ====
    if ($method === 'POST') {
        $input  = json_decode(file_get_contents('php://input'), true) ?: [];
        $action = $input['action'] ?? '';

        switch ($action) {
            case 'create':
            case 'update':
                // собираем данные
                $ip        = trim($input['data']['ip_address']   ?? '');
                $mask      = trim($input['data']['subnet_mask']   ?? '');
                $gw        = trim($input['data']['gateway']       ?? '');
                $dns       = trim($input['data']['dns_servers']   ?? '');
                $eqId      = (int)($input['data']['equipment_id'] ?? 0);
                
                // валидация IPv4
                foreach (['IP-адрес'=>$ip,'Маска'=>$mask,'Шлюз'=>$gw] as $label => $val) {
                    if (!filter_var($val, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                        http_response_code(400);
                        echo json_encode(['status'=>'error','message'=>"Неверный формат {$label}: {$val}"], JSON_UNESCAPED_UNICODE);
                        exit;
                    }
                }
                // валидация DNS (может быть несколько, через запятую)
                if ($dns !== '') {
                    $parts = array_map('trim', explode(',', $dns));
                    foreach ($parts as $d) {
                        if (!filter_var($d, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                            http_response_code(400);
                            echo json_encode(['status'=>'error','message'=>"Неверный DNS-сервер: {$d}"], JSON_UNESCAPED_UNICODE);
                            exit;
                        }
                    }
                    $dns = implode(',', $parts);
                }

                // создаём модель
                $id = $action==='update' ? (int)$input['id'] : null;
                $ns = new NetworkSettings(
                    $id,
                    $ip,
                    $eqId,
                    $mask,
                    $gw,
                    $dns
                );
                if ($action==='create') {
                    NetworksettingsContext::add($ns);
                } else {
                    NetworksettingsContext::update($ns);
                }
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

    // всё остальное — ошибочный запрос
    http_response_code(400);
    echo json_encode(['status'=>'error','message'=>'Invalid request'], JSON_UNESCAPED_UNICODE);
    exit;

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['status'=>'error','message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}
