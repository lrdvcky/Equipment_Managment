<?php
// controllers/SoftwareController.php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../models/SoftwareContext.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($method === 'GET' && $action === 'get') {
    $all = SoftwareContext::getAll();
    echo json_encode(array_map(fn($s)=>get_object_vars($s), $all), JSON_UNESCAPED_UNICODE);
    exit;
}

if ($method === 'POST') {
    try {
        // Для multipart/form-data (с FormData и полем equipment_ids[])
        $data = [
            'name'           => trim($_POST['name']           ?? ''),
            'version'        => trim($_POST['version']        ?? '') ?: null,
            'developer_name' => trim($_POST['developer_name'] ?? '') ?: null,
            'equipment_ids'  => $_POST['equipment_ids'] ?? []    // массив ID
        ];
        switch ($action) {
            case 'create':
                $newId = SoftwareContext::create($data);
                echo json_encode(['status'=>'success','id'=>$newId], JSON_UNESCAPED_UNICODE);
                break;
            case 'update':
                $id = (int)($_POST['id'] ?? 0);
                SoftwareContext::update($id, $data);
                echo json_encode(['status'=>'success'], JSON_UNESCAPED_UNICODE);
                break;
            case 'destroy':
                $id = (int)($_POST['id'] ?? 0);
                SoftwareContext::delete($id);
                echo json_encode(['status'=>'success'], JSON_UNESCAPED_UNICODE);
                break;
            default:
                http_response_code(400);
                echo json_encode(['status'=>'error','message'=>"Unknown action '{$action}'"], JSON_UNESCAPED_UNICODE);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status'=>'error','message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

http_response_code(400);
echo json_encode(['status'=>'error','message'=>'Invalid request'], JSON_UNESCAPED_UNICODE);
exit;
