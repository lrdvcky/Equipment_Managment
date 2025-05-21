<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../models/RoomContext.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($method==='GET' && $action==='get') {
    $list = RoomContext::getAll();
    echo json_encode(array_map(fn($r)=>get_object_vars($r), $list), JSON_UNESCAPED_UNICODE);
    exit;
}

if ($method==='POST') {
    try {
        switch ($action) {
            case 'create':
                $id = RoomContext::create(
                    trim($_POST['name'] ?? ''),
                    trim($_POST['short_name'] ?? ''),
                    $_POST['responsible_user_id'] ?: null,
                    $_POST['temporary_responsible_user_id'] ?: null
                );
                echo json_encode(['status'=>'success','id'=>$id], JSON_UNESCAPED_UNICODE);
                break;
            case 'update':
                RoomContext::update(
                    (int)($_POST['id'] ?? 0),
                    trim($_POST['name'] ?? ''),
                    trim($_POST['short_name'] ?? ''),
                    $_POST['responsible_user_id'] ?: null,
                    $_POST['temporary_responsible_user_id'] ?: null
                );
                echo json_encode(['status'=>'success'], JSON_UNESCAPED_UNICODE);
                break;
            case 'destroy':
                RoomContext::delete((int)($_POST['id'] ?? 0));
                echo json_encode(['status'=>'success'], JSON_UNESCAPED_UNICODE);
                break;
            default:
                http_response_code(400);
                echo json_encode(['status'=>'error','message'=>"Unknown action {$action}"], JSON_UNESCAPED_UNICODE);
        }
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(['status'=>'error','message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

http_response_code(400);
echo json_encode(['status'=>'error','message'=>'Invalid request'], JSON_UNESCAPED_UNICODE);
exit;
