<?php
require_once __DIR__ . '/../connection.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/UsersContext.php';

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET' && ($_GET['action'] ?? '') === 'get') {
    $users = array_map(fn($u) => get_object_vars($u), UsersContext::getAllUsers());
    echo json_encode($users);
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $input['action'] ?? '';
    try {
        switch ($action) {
            case 'create':
                $newId = UsersContext::createUser($input['data']);
                echo json_encode(['status'=>'success', 'id'=>$newId]);
                break;
            case 'update':
                UsersContext::updateUser((int)$input['id'], $input['data']);
                echo json_encode(['status'=>'success']);
                break;
            case 'delete':
                UsersContext::deleteUser((int)$input['id']);
                echo json_encode(['status'=>'success']);
                break;
            default:
                throw new Exception("Unknown action");
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status'=>'error', 'message'=>$e->getMessage()]);
    }
    exit;
}

http_response_code(400);
echo json_encode(['status'=>'error','message'=>'Invalid request']);
exit;
