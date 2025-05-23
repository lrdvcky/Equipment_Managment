<?php
// controllers/EquipmentController.php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../models/EquipmentContext.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($method === 'GET' && $action === 'get') {
    $items = EquipmentContext::getAll();
    $out = array_map(fn($e) => get_object_vars($e), $items);
    echo json_encode($out, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($method === 'POST') {
    try {
        switch ($action) {
            case 'create':
                $data = parseRequest();
                $newId = EquipmentContext::create($data);
                echo json_encode(['status' => 'success', 'id' => $newId], JSON_UNESCAPED_UNICODE);
                break;

            case 'update':
                $id   = (int)($_POST['id'] ?? 0);
                $data = parseRequest($allowNoPhoto = true);
                EquipmentContext::update($id, $data);
                echo json_encode(['status' => 'success'], JSON_UNESCAPED_UNICODE);
                break;

            case 'destroy':
                $id = (int)($_POST['id'] ?? 0);
                EquipmentContext::delete($id);
                echo json_encode(['status' => 'success'], JSON_UNESCAPED_UNICODE);
                break;

            default:
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => "Unknown action '{$action}'"], JSON_UNESCAPED_UNICODE);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// любой другой запрос — 400
http_response_code(400);
echo json_encode(['status' => 'error', 'message' => 'Invalid request'], JSON_UNESCAPED_UNICODE);
exit;


/**
 * Собирает из $_POST и $_FILES массив для create/update
 * @param bool $allowNoPhoto — при update не обязательно присылать фото
 * @return array
 */
function parseRequest(bool $allowNoPhoto = false): array {
    $fields = [
        'name',
        'inventory_number',
        'room_id',
        'responsible_user_id',
        'temporary_responsible_user_id',
        'price',
        'model_id',
        'direction_name',
        'status',
        'comment',
        'equipment_type',
        'inventory_section'   // ← добавлено поле
    ];

    $data = [];
    foreach ($fields as $f) {
        $data[$f] = array_key_exists($f, $_POST) && $_POST[$f] !== ''
                    ? $_POST[$f]
                    : null;
    }

    // 🔒 Валидация inventory_number: только цифры от 1 до 999
    if (!isset($data['inventory_number'])
        || !ctype_digit((string)$data['inventory_number'])
        || (int)$data['inventory_number'] < 1
        || (int)$data['inventory_number'] > 999
    ) {
        throw new Exception('Инвентарный номер должен быть целым числом от 1 до 999.');
    }

    // 📷 Обработка файла фото
    if (isset($_FILES['photo']) && is_uploaded_file($_FILES['photo']['tmp_name'])) {
        $data['photo'] = file_get_contents($_FILES['photo']['tmp_name']);
    } elseif (!$allowNoPhoto) {
        $data['photo'] = null;
    }

    return $data;
}
