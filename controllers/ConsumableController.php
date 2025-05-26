<?php
declare(strict_types=1);
session_start();
ini_set('display_errors','1');
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../connection.php';
require_once __DIR__ . '/../models/ConsumableContext.php';
require_once __DIR__ . '/../models/ConsumableResponsibleHistoryContext.php';
require_once __DIR__ . '/../models/UsersContext.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? '';

try {
    $pdo = OpenConnection();

    // --- GET ---
    if ($method === 'GET') {
        switch ($action) {
            // 1) Список
            case 'get':
                $items = ConsumableContext::getAll();
                $out = [];
                foreach ($items as $c) {
                    $row = get_object_vars($c);

                    // фото → data-uri
                    if (!empty($row['image'])) {
                        $b64 = base64_encode($row['image']);
                        $row['photo'] = "data:image/jpeg;base64,{$b64}";
                    } else {
                        $row['photo'] = null;
                    }
                    unset($row['image']);

                    // тип
                    $stmt = $pdo->prepare("SELECT name FROM `ConsumableType` WHERE id = ?");
                    $stmt->execute([$c->consumable_type_id]);
                    $row['type_name'] = $stmt->fetchColumn() ?: '';

                    // свойства
                    $stmt = $pdo->prepare(
                      "SELECT property_name, property_value 
                         FROM `ConsumableProperty` 
                        WHERE consumable_id = ?"
                    );
                    $stmt->execute([$c->id]);
                    $props = [];
                    while ($pr = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $props[] = "{$pr['property_name']}: {$pr['property_value']}";
                    }
                    $row['properties'] = implode(', ', $props);

                    // ответственные
                    $stmt = $pdo->prepare(
                      "SELECT CONCAT_WS(' ', last_name, first_name, middle_name)
                         FROM `User` WHERE id = ?"
                    );
                    $stmt->execute([$c->responsible_user_id]);
                    $row['responsible_name'] = $stmt->fetchColumn() ?: '';
                    $stmt->execute([$c->temporary_responsible_user_id]);
                    $row['temporary_responsible_name'] = $stmt->fetchColumn() ?: '';

                    $out[] = $row;
                }
                echo json_encode($out, JSON_UNESCAPED_UNICODE);
                exit;

            // 2) История смен ответственных
            case 'getHistory':
                $cid = (int)($_GET['consumable'] ?? 0);
                $hist = ConsumableResponsibleHistoryContext::getByConsumable($cid);
                $res = [];
                foreach ($hist as $h) {
                    $res[] = [
                        'changed_at' => $h->changed_at,
                        'comment'    => $h->comment,
                        'user_name'  => UsersContext::getFullNameById($h->user_id),
                    ];
                }
                echo json_encode($res, JSON_UNESCAPED_UNICODE);
                exit;

            // 3) Типы расходников
            case 'getTypes':
                $types = $pdo->query("SELECT id, name FROM `ConsumableType`")
                             ->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($types, JSON_UNESCAPED_UNICODE);
                exit;

            // 4) Пользователи
            case 'getUsers':
                $users = $pdo->query(
                  "SELECT id, CONCAT_WS(' ', last_name, first_name, middle_name) AS name
                     FROM `User`"
                )->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($users, JSON_UNESCAPED_UNICODE);
                exit;

            default:
                throw new Exception("Invalid GET action");
        }
    }

    // --- POST ---
    if ($method === 'POST') {
        // 1) прочие поля
        $name  = trim($_POST['name'] ?? '');
        $desc  = trim($_POST['description'] ?? '') ?: null;
        $date  = trim($_POST['arrival_date'] ?? '');
        $qty   = (int)($_POST['quantity'] ?? 0);
        $type  = (int)($_POST['consumable_type_id'] ?? 0);
        $resp  = (int)($_POST['responsible_user_id'] ?? 0);
        $temp  = (int)($_POST['temporary_responsible_user_id'] ?? 0);
        $hcomm = trim($_POST['history_comment'] ?? '');

        // 2) конвертация даты из DD.MM.YYYY → YYYY-MM-DD (если надо)
        if (preg_match('#^(\d{2})\.(\d{2})\.(\d{4})$#', $date, $m)) {
            $date = "{$m[3]}-{$m[2]}-{$m[1]}";
        }
        // валидный ISO-формат
        if (!preg_match('#^\d{4}-\d{2}-\d{2}$#', $date)) {
            throw new Exception('Дата должна быть в формате ГГГГ-ММ-ДД или ДД.MM.ГГГГ');
        }
        if ($qty < 1) {
            throw new Exception('Количество должно быть положительным целым числом');
        }

        // 3) фото
        $photo = null;
        if (isset($_FILES['photo']) && is_uploaded_file($_FILES['photo']['tmp_name'])) {
            $photo = file_get_contents($_FILES['photo']['tmp_name']);
        }

        switch ($_POST['action'] ?? '') {
            // CREATE
            case 'create':
                $sql = "INSERT INTO `Consumable`
                  (name, description, arrival_date, image, quantity,
                   responsible_user_id, temporary_responsible_user_id,
                   consumable_type_id)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                  $name, $desc, $date, $photo, $qty,
                  $resp, $temp, $type
                ]);
                echo json_encode(['status'=>'success'], JSON_UNESCAPED_UNICODE);
                exit;

            // UPDATE
            case 'update':
                $id = (int)($_POST['id'] ?? 0);
                // старый ответственный?
                $old = $pdo->prepare("SELECT responsible_user_id FROM `Consumable` WHERE id=?");
                $old->execute([$id]);
                $oldResp = (int)$old->fetchColumn();

                // собираем SET-часть
                $sets = [
                  'name = :name',
                  'description = :desc',
                  'arrival_date = :date',
                  'quantity = :qty',
                  'responsible_user_id = :resp',
                  'temporary_responsible_user_id = :temp',
                  'consumable_type_id = :type',
                ];
                $params = [
                  ':name' => $name,
                  ':desc' => $desc,
                  ':date' => $date,
                  ':qty'  => $qty,
                  ':resp' => $resp,
                  ':temp' => $temp,
                  ':type' => $type,
                  ':id'   => $id,
                ];
                // если загрузили новый файл — меняем image
                if ($photo !== null) {
                    $sets[] = 'image = :img';
                    $params[':img'] = $photo;
                }

                $sql = "UPDATE `Consumable`
                           SET " . implode(', ', $sets) . "
                         WHERE id = :id";
                $pdo->prepare($sql)->execute($params);

                // история, если сменился ответственный
                if ($oldResp !== $resp) {
                    ConsumableResponsibleHistoryContext::add($id, $resp, $hcomm);
                }

                echo json_encode(['status'=>'success'], JSON_UNESCAPED_UNICODE);
                exit;

            // DELETE
            case 'destroy':
                $pdo->prepare("DELETE FROM `Consumable` WHERE id = ?")
                    ->execute([(int)$_POST['id']]);
                echo json_encode(['status'=>'success'], JSON_UNESCAPED_UNICODE);
                exit;

            default:
                throw new Exception("Unknown POST action");
        }
    }

    throw new Exception("Invalid request method");
}
catch (Throwable $e) {
    http_response_code(400);
    echo json_encode([
      'status'  => 'error',
      'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
