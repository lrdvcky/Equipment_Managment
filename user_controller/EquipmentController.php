<?php
require_once '../user_context/EquipmentContext.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'getByUser') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['error' => 'Unauthorized']);
        http_response_code(401);
        exit;
    }

    $userId = $_SESSION['user_id'];
    echo json_encode(EquipmentContext::getUserEquipment($userId));
    exit;
}
