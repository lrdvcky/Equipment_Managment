<?php
session_start();
require_once '../user_context/EquipmentContext.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];

if ($method === 'GET') {
    if ($action === 'getByUser') {
        $data = EquipmentContext::getUserEquipment($userId);
        echo json_encode($data);
        exit;
    }
}

http_response_code(400);
echo json_encode(['error' => 'Bad request']);
