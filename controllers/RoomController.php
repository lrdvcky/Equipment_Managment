<?php
// controllers/RoomController.php

// Отключаем вывод «сырого» HTML-ошибок
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../connection.php';
require_once __DIR__ . '/../models/RoomContext.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['action'] ?? '') === 'get') {
        $conn  = OpenConnection();
        $rooms = RoomContext::getAll();
        $out   = [];

        foreach ($rooms as $room) {
            // Превращаем объект Room в массив
            $row = get_object_vars($room);

            // Забираем ФИО «ответственного»
            $stmt = $conn->prepare("
                SELECT CONCAT_WS(' ', last_name, first_name, middle_name)
                FROM `User`
                WHERE id = ?
            ");
            $stmt->execute([$room->responsible_user_id]);
            $row['responsible_name'] = $stmt->fetchColumn() ?: '';

            // Забираем ФИО «временно ответственного»
            $stmt = $conn->prepare("
                SELECT CONCAT_WS(' ', last_name, first_name, middle_name)
                FROM `User`
                WHERE id = ?
            ");
            $stmt->execute([$room->temporary_responsible_user_id]);
            $row['temporary_responsible_name'] = $stmt->fetchColumn() ?: '';

            $out[] = $row;
        }

        echo json_encode($out, JSON_UNESCAPED_UNICODE);
        exit;
    }
    throw new Exception('Invalid request');
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
?>