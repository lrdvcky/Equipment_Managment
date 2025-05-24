<?php
declare(strict_types=1);
session_start();
ini_set('display_errors','1');
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../connection.php';
require_once __DIR__ . '/../models/ConsumableContext.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? '';

try {
    if ($method === 'GET') {
        switch ($action) {
            case 'get':
                $items = ConsumableContext::getAll();
                $out = [];
                foreach ($items as $c) {
                    $row = get_object_vars($c);
                    // тип
                    $stmt = OpenConnection()->prepare("SELECT name FROM `ConsumableType` WHERE id = ?");
                    $stmt->execute([$c->consumable_type_id]);
                    $row['type_name'] = $stmt->fetchColumn() ?: '';
                    // свойства
                    $stmt = OpenConnection()->prepare("
                        SELECT property_name, property_value 
                        FROM `ConsumableProperty` WHERE consumable_id = ?
                    ");
                    $stmt->execute([$c->id]);
                    $props = [];
                    while($pr = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $props[] = "{$pr['property_name']}: {$pr['property_value']}";
                    }
                    $row['properties'] = implode(', ', $props);
                    // ответственные
                    $stmt = OpenConnection()->prepare("
                        SELECT CONCAT_WS(' ', last_name, first_name, middle_name)
                        FROM `User` WHERE id = ?
                    ");
                    $stmt->execute([$c->responsible_user_id]);
                    $row['responsible_name'] = $stmt->fetchColumn() ?: '';
                    $stmt->execute([$c->temporary_responsible_user_id]);
                    $row['temporary_responsible_name'] = $stmt->fetchColumn() ?: '';
                    $out[] = $row;
                }
                echo json_encode($out, JSON_UNESCAPED_UNICODE);
                exit;

            case 'getTypes':
                $pdo = OpenConnection();
                $types = $pdo->query("SELECT id,name FROM `ConsumableType`")
                             ->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($types, JSON_UNESCAPED_UNICODE);
                exit;

            case 'getUsers':
                $pdo = OpenConnection();
                $users = $pdo->query("
                  SELECT id, CONCAT_WS(' ', last_name, first_name, middle_name) AS name
                  FROM `User`
                ")->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($users, JSON_UNESCAPED_UNICODE);
                exit;

            default:
                throw new Exception('Invalid GET action');
        }
    }

    if ($method === 'POST') {
        switch ($_POST['action'] ?? '') {
            case 'create':
                // собираем данные
                $c = new Consumable(
                  null,
                  $_POST['name'] ?? '',
                  $_POST['description'] ?? null,
                  $_POST['arrival_date'] ?? null,
                  null,  // image (не используем)
                  (int)($_POST['quantity'] ?? 0),
                  (int)($_POST['responsible_user_id'] ?? 0),
                  (int)($_POST['temporary_responsible_user_id'] ?? 0),
                  (int)($_POST['consumable_type_id'] ?? 0)
                );
                ConsumableContext::add($c);
                echo json_encode(['status'=>'success'], JSON_UNESCAPED_UNICODE);
                exit;

            case 'update':
    // собираем данные из $_POST
    $id   = (int)$_POST['id'];
    $name = $_POST['name'] ?? '';
    $desc = $_POST['description'] ?? '';
    $date = $_POST['arrival_date'] ?? null;
    $qty  = (int)$_POST['quantity'];
    $type = (int)$_POST['consumable_type_id'];
    $resp = (int)$_POST['responsible_user_id'];
    $temp = (int)$_POST['temporary_responsible_user_id'];

    // Вот здесь начинается правильный UPDATE
    $sql = "
      UPDATE `Consumable` SET
        `name`                        = :name,
        `description`                 = :description,
        `arrival_date`                = :arrival_date,
        `quantity`                    = :quantity,
        `consumable_type_id`          = :type,
        `responsible_user_id`         = :resp,
        `temporary_responsible_user_id` = :temp
      WHERE `id` = :id
    ";
    $stmt = OpenConnection()->prepare($sql);
    $stmt->execute([
      ':name'          => $name,
      ':description'   => $desc,
      ':arrival_date'  => $date,
      ':quantity'      => $qty,
      ':type'          => $type,
      ':resp'          => $resp,
      ':temp'          => $temp,
      ':id'            => $id,
    ]);
    echo json_encode(['status'=>'success'], JSON_UNESCAPED_UNICODE);
    exit;

            case 'destroy':
                ConsumableContext::delete((int)($_POST['id'] ?? 0));
                echo json_encode(['status'=>'success'], JSON_UNESCAPED_UNICODE);
                exit;

            default:
                throw new Exception('Invalid POST action');
        }
    }

    throw new Exception('Invalid request method');
    
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['status'=>'error','message'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}
