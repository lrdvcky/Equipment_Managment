<?php
// controllers/EquipmentController.php

ini_set('display_errors', 0);
error_reporting(0);

require_once __DIR__ . '/../models/EquipmentContext.php';
require_once __DIR__ . '/../models/EquipmentResponsibleHistoryContext.php';
require_once __DIR__ . '/../models/UsersContext.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($method === 'GET') {
    // список
    if ($action === 'get') {
        $items = EquipmentContext::getAll();
        $out   = array_map(fn($e) => get_object_vars($e), $items);
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // история смен ответственных
    if ($action === 'getHistory') {
        $equipId = (int)($_GET['equip'] ?? 0);
        $history = EquipmentResponsibleHistoryContext::getByEquipment($equipId);
        $out = array_map(fn($h) => [
            'changed_at' => $h->changed_at,
            'comment'    => $h->comment,
            'user_name'  => UsersContext::getFullNameById($h->user_id)
        ], $history);
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if ($method === 'POST') {
    try {
        switch ($action) {
            case 'create':
                $data  = parseRequest();
                $newId = EquipmentContext::create($data);
                echo json_encode(['status' => 'success', 'id' => $newId], JSON_UNESCAPED_UNICODE);
                break;

            case 'update':
                $id       = (int)($_POST['id'] ?? 0);
                $data     = parseRequest($allowNoPhoto = true);
                $original = EquipmentContext::getById($id);
                EquipmentContext::update($id, $data);

                // в историю, если сменился ответственный
                if (isset($data['responsible_user_id'])
                    && $original->responsible_user_id != $data['responsible_user_id']
                ) {
                    EquipmentResponsibleHistoryContext::add(
                        $id,
                        $data['responsible_user_id'],
                        $_POST['history_comment'] ?? ''
                    );
                }

                echo json_encode(['status' => 'success'], JSON_UNESCAPED_UNICODE);
                break;

            case 'destroy':
                $id = (int)($_POST['id'] ?? 0);
                EquipmentContext::delete($id);
                echo json_encode(['status' => 'success'], JSON_UNESCAPED_UNICODE);
                break;

            default:
                http_response_code(400);
                echo json_encode(
                    ['status' => 'error', 'message' => "Unknown action '{$action}'"],
                    JSON_UNESCAPED_UNICODE
                );
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

http_response_code(400);
echo json_encode(['status' => 'error', 'message' => 'Invalid request'], JSON_UNESCAPED_UNICODE);
exit;


/**
 * Собирает из $_POST и $_FILES нужные поля
 * @param bool $allowNoPhoto
 * @return array
 * @throws Exception
 */
function parseRequest(bool $allowNoPhoto = false): array {
    $fields = [
        'name','inventory_number','room_id',
        'responsible_user_id','temporary_responsible_user_id',
        'price','model_name','direction_name',
        'status','comment','equipment_type','inventory_section'
    ];

    $data = [];
    foreach ($fields as $f) {
        $data[$f] = array_key_exists($f, $_POST) && $_POST[$f] !== ''
                    ? $_POST[$f]
                    : null;
    }

    // валидация номера
    if (!isset($data['inventory_number'])
        || !ctype_digit((string)$data['inventory_number'])
        || (int)$data['inventory_number'] < 1
        || (int)$data['inventory_number'] > 1000000000000000000
    )

    // фото
    if (isset($_FILES['photo']) && is_uploaded_file($_FILES['photo']['tmp_name'])) {
        $data['photo'] = file_get_contents($_FILES['photo']['tmp_name']);
    } elseif (!$allowNoPhoto) {
        $data['photo'] = null;
    }

    return $data;
}
