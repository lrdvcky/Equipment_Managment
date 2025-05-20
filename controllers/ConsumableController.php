<?php
declare(strict_types=1);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../connection.php';
require_once __DIR__ . '/../models/ConsumableContext.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET' || ($_GET['action'] ?? '') !== 'get') {
        throw new Exception('Invalid request');
    }

    $pdo   = OpenConnection();
    $items = ConsumableContext::getAll();
    $out   = [];

    foreach ($items as $c) {
        $row = get_object_vars($c);

        $stmt = $pdo->prepare("SELECT name FROM `ConsumableType` WHERE id = ?");
        $stmt->execute([$c->consumable_type_id]);
        $row['type_name'] = $stmt->fetchColumn() ?: '';
        $stmt->execute();
        $stmt = $pdo->prepare("
            SELECT property_name, property_value
            FROM `ConsumableProperty`
            WHERE consumable_id = ?
        ");
        $stmt->execute([$c->id]);
        $props = [];
        while ($pr = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $props[] = "{$pr['property_name']}: {$pr['property_value']}";
        }
        $row['properties'] = implode(', ', $props);

        $stmt = $pdo->prepare("
            SELECT CONCAT_WS(' ', last_name, first_name, middle_name)
            FROM `User` WHERE id = ?
        ");
        $stmt->execute([$c->responsible_user_id]);
        $row['responsible_name'] = $stmt->fetchColumn() ?: '';

        $stmt->execute([$c->temporary_responsible_user_id]);
        $row['temporary_responsible_name'] = $stmt->fetchColumn() ?: '';

        $out[] = $row;
    }

    if (ob_get_level()) ob_end_clean();
    echo json_encode($out, JSON_UNESCAPED_UNICODE);
    exit;
} catch (Throwable $e) {
    http_response_code(400);
    if (ob_get_level()) ob_end_clean();
    echo json_encode(['status'=>'error','message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}
?>