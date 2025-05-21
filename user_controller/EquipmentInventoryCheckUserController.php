<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Подключение к базе и контекст
require_once '../connection.php';
require_once '../user_context/EquipmentInventoryCheckUserContext.php';

header('Content-Type: application/json');

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

$userId = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

// Получение закреплённого оборудования
if ($method === 'GET') {
    try {
        $result = EquipmentInventoryCheckUserContext::getByUser($userId);
        echo json_encode($result);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// Отправка результатов инвентаризации
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // Валидация
    if (!isset($input['equipment_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing equipment_id']);
        exit;
    }

    $input['checked_by_user_id'] = $userId;
    $input['check'] = isset($input['check']) ? (bool)$input['check'] : false;

    try {
        $success = EquipmentInventoryCheckUserContext::submitCheck($input);
        echo json_encode(['success' => $success]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// Неверный метод
http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
exit;
?>
