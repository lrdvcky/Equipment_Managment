<?php

require_once __DIR__ . '/../connection.php';
require_once __DIR__ . '/../models/SoftwareContext.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['action'] ?? '') === 'get') {
        $arr = SoftwareContext::getAll();
        $out = array_map(fn($s) => get_object_vars($s), $arr);
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
        exit;
    }
    throw new Exception('Invalid request');
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
    exit;
}
