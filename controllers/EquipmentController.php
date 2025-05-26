<?php
ini_set('display_errors', 0);
error_reporting(0);

require_once __DIR__ . '/../models/EquipmentContext.php';
require_once __DIR__ . '/../models/EquipmentResponsibleHistoryContext.php';
require_once __DIR__ . '/../models/UsersContext.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    if ($method === 'GET') {
        switch ($action) {
            case 'get':
                $items = EquipmentContext::getAll();
                echo json_encode(array_map('get_object_vars', $items), JSON_UNESCAPED_UNICODE);
                exit;
            case 'getHistory':
                $equipId = (int)($_GET['equip'] ?? 0);
                $history = EquipmentResponsibleHistoryContext::getByEquipment($equipId);
                $out = array_map(fn($h) => [
                    'changed_at' => $h->changed_at,
                    'comment'    => $h->comment,
                    'user_name'  => UsersContext::getFullNameById($h->user_id)
                ], $history);
                echo json_encode($out, JSON_UNESCAPED_UNICODE);
                exit;
            default:
                throw new Exception('Bad request');
        }
    }

    if ($method === 'POST') {
        switch ($action) {
            case 'create':
                $data  = parseRequest();
                $newId = EquipmentContext::create($data);
                echo json_encode(['status' => 'success', 'id' => $newId], JSON_UNESCAPED_UNICODE);
                break;
            case 'update':
                $id   = (int)($_POST['id'] ?? 0);
                $data = parseRequest(true);
                $original = EquipmentContext::getById($id);
                EquipmentContext::update($id, $data);
                // логируем смену ответственного
                if (isset($data['responsible_user_id']) && $original && $original->responsible_user_id != $data['responsible_user_id']) {
                    EquipmentResponsibleHistoryContext::add($id, $data['responsible_user_id'], $_POST['history_comment'] ?? '');
                }
                echo json_encode(['status' => 'success'], JSON_UNESCAPED_UNICODE);
                break;
            case 'destroy':
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
        http_response_code(400);
        echo json_encode(['status'=>'error','message'=>'ID не передан'], JSON_UNESCAPED_UNICODE);
        break;
    }

    try {
        EquipmentContext::delete($id);
        echo json_encode(['status' => 'success'], JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        // если мешают внешние ключи, отдадим понятное сообщение
        http_response_code(400);
        echo json_encode([
            'status'  => 'error',
            'message' => 'Невозможно удалить: запись используется в других таблицах'
        ], JSON_UNESCAPED_UNICODE);
    }
    break;

            default:
                throw new Exception('Unknown action');
        }
        exit;
    }

    throw new Exception('Invalid request');
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Сбор и валидация входных данных
 * @param bool $allowNoPhoto
 * @return array
 * @throws Exception
 */
function parseRequest(bool $allowNoPhoto = false): array {
    $fields = [
        'name', 'inventory_number', 'room_id',
        'responsible_user_id', 'temporary_responsible_user_id',
        'price', 'model_name', 'direction_name',
        'status', 'comment', 'equipment_type', 'inventory_section'
    ];

    $data = [];
    foreach ($fields as $f) {
        $data[$f] = $_POST[$f] ?? null;
    }

    // обязательные проверки
    foreach (['name','inventory_number','room_id','responsible_user_id','model_name','equipment_type','inventory_section'] as $must) {
        if (empty($data[$must])) throw new Exception('Заполнены не все обязательные поля');
    }

    // валидация стоимости
    if ($data['price'] !== null && $data['price'] !== '' && !preg_match('/^\d+(\.\d+)?$/', $data['price'])) {
        throw new Exception('Стоимость должна быть числом');
    }

    // файл
    if (isset($_FILES['photo']) && is_uploaded_file($_FILES['photo']['tmp_name'])) {
        $data['photo'] = file_get_contents($_FILES['photo']['tmp_name']);
    } elseif (!$allowNoPhoto) {
        $data['photo'] = null; // при создании фото не обязательно
    }

    return $data;
}