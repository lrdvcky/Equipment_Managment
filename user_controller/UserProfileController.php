<?php
session_start();
header('Content-Type: application/json');

require_once '../user_context/UserContext.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Не авторизован']);
    exit;
}

$user = UserContext::getUserById($_SESSION['user_id']);

if ($user) {
    echo json_encode([
        'full_name' => $user->full_name,
        'username' => $user->username,
        'email' => $user->email,
        'phone' => $user->phone,
        'address' => $user->address,
        'role' => $user->role
    ]);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Пользователь не найден']);
}
?>
