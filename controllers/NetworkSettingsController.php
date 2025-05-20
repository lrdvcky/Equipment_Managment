<?php
require_once '../connection.php';
require_once '../models/NetworkSettings.php';
require_once '../models/NetworkSettingsContext.php';

class NetworkSettingsController {

    public static function index(): array {
        return NetworkSettingsContext::getAll();
    }

    public static function store(array $data): bool {
        $settings = new NetworkSettings(
            0,
            $data['ip_address'],
            $data['subnet_mask'] ?? null,
            $data['gateway'] ?? null,
            $data['dns_servers'] ?? null,
            $data['equipment_id'] ?? null
        );
        return NetworkSettingsContext::add($settings);
    }

    public static function update(int $id, array $data): bool {
        $settings = new NetworkSettings(
            $id,
            $data['ip_address'],
            $data['subnet_mask'] ?? null,
            $data['gateway'] ?? null,
            $data['dns_servers'] ?? null,
            $data['equipment_id'] ?? null
        );
        return NetworkSettingsContext::update($settings);
    }

    public static function destroy(int $id): bool {
        return NetworkSettingsContext::delete($id);
    }
}

// … header, require_once, класс NetworkSettingsController …

// AJAX-точка входа
header('Content-Type: application/json; charset=utf-8');
try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['action'] ?? '') === 'get') {
        $pdo   = OpenConnection();
        // Предполагаем, что индекс возвращает объекты NetworkSetting
        $data  = NetworkSettingsController::index();
        $out   = [];

        foreach ($data as $ns) {
            // Превращаем объект в массив
            $row = get_object_vars($ns);

            // Подтягиваем название оборудования
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
