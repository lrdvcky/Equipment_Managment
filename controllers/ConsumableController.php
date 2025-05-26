<?php
/**
 * controllers/ConsumableController.php
 * Полный контроллер для CRUD-операций с расходными материалами.
 * — GET:
 *      ?action=get            – список
 *      ?action=getHistory     – история смен ответственных
 *      ?action=getTypes       – типы расходников
 *      ?action=getUsers       – пользователи (id + ФИО)
 * — POST:
 *      action=create|update|delete|destroy
 */

declare(strict_types=1);
session_start();
ini_set('display_errors', '1');
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../connection.php';
require_once __DIR__ . '/../models/ConsumableContext.php';
require_once __DIR__ . '/../models/ConsumableResponsibleHistoryContext.php';
require_once __DIR__ . '/../models/UsersContext.php';

$pdo    = OpenConnection();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? '';

try {
    /* ======== GET ======== */
    if ($method === 'GET') {
        switch ($action) {
            /* --- 1. Список расходников --- */
            case 'get':
                $items = ConsumableContext::getAll();
                $out   = [];

                foreach ($items as $c) {
                    $row = get_object_vars($c);

                    // картинка → data-URI
                    $row['photo'] = $row['image']
                        ? 'data:image/jpeg;base64,' . base64_encode($row['image'])
                        : null;
                    unset($row['image']);

                    // тип расходника
                    $row['type_name'] = $pdo
                        ->prepare('SELECT name FROM ConsumableType WHERE id = ?')
                        ->execute([$c->consumable_type_id])
                        ? $pdo->query('SELECT name FROM ConsumableType WHERE id = ' . $c->consumable_type_id)->fetchColumn()
                        : '';

                    // свойства
                    $propsStmt = $pdo->prepare('SELECT property_name,property_value FROM ConsumableProperty WHERE consumable_id=?');
                    $propsStmt->execute([$c->id]);
                    $props = [];
                    while ($pr = $propsStmt->fetch(PDO::FETCH_ASSOC)) {
                        $props[] = "{$pr['property_name']}: {$pr['property_value']}";
                    }
                    $row['properties'] = implode(', ', $props);

                    // ответственный / временный ответственный
                    $uStmt = $pdo->prepare('SELECT CONCAT_WS(" ",last_name,first_name,middle_name) FROM User WHERE id = ?');
                    $uStmt->execute([$c->responsible_user_id]);
                    $row['responsible_name'] = $uStmt->fetchColumn() ?: '';

                    $uStmt->execute([$c->temporary_responsible_user_id]);
                    $row['temporary_responsible_name'] = $uStmt->fetchColumn() ?: '';

                    $out[] = $row;
                }
                echo json_encode($out, JSON_UNESCAPED_UNICODE);
                exit;

            /* --- 2. История смен ответственных --- */
            case 'getHistory':
                $cid  = (int)($_GET['consumable'] ?? 0);
                $hist = ConsumableResponsibleHistoryContext::getByConsumable($cid);
                $res  = [];
                foreach ($hist as $h) {
                    $res[] = [
                        'changed_at' => $h->changed_at,
                        'comment'    => $h->comment,
                        'user_name'  => UsersContext::getFullNameById($h->user_id),
                    ];
                }
                echo json_encode($res, JSON_UNESCAPED_UNICODE);
                exit;

            /* --- 3. Типы расходников --- */
            case 'getTypes':
                $types = $pdo->query('SELECT id,name FROM ConsumableType')->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($types, JSON_UNESCAPED_UNICODE);
                exit;

            /* --- 4. Пользователи --- */
            case 'getUsers':
                $users = $pdo->query(
                    'SELECT id, CONCAT_WS(" ", last_name, first_name, middle_name) AS name FROM User'
                )->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($users, JSON_UNESCAPED_UNICODE);
                exit;

            default:
                throw new Exception('Invalid GET action');
        }
    }

    /* ======== POST ======== */
    if ($method === 'POST') {
        $postAction = $_POST['action'] ?? '';

        /* ---------- DELETE / DESTROY ---------- */
        if ($postAction === 'delete' || $postAction === 'destroy') {
            $id = (int)($_POST['id'] ?? 0);
            if (!$id) {
                throw new Exception('ID не передан');
            }

            ConsumableContext::delete($id);   // может бросить PDOException
            echo json_encode(['status' => 'success'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        /* ---------- CREATE / UPDATE ---------- */
        /* 1. Читаем поля формы */
        $name  = trim($_POST['name'] ?? '');
        $desc  = trim($_POST['description'] ?? '') ?: null;
        $date  = trim($_POST['arrival_date'] ?? '');
        $qty   = (int)($_POST['quantity'] ?? 0);
        $type  = (int)($_POST['consumable_type_id'] ?? 0);
        $resp  = (int)($_POST['responsible_user_id'] ?? 0);
        $temp  = (int)($_POST['temporary_responsible_user_id'] ?? 0);
        $hcomm = trim($_POST['history_comment'] ?? '');

        /* 2. Проверяем дату ДД.ММ.ГГГГ  и конвертируем в YYYY-MM-DD */
        // если случайно пришёл HTML-формат 2025-03-01 – преобразуем
        if (preg_match('#^(\d{4})-(\d{2})-(\d{2})$#', $date, $mHtml)) {
            $date = "{$mHtml[3]}.{$mHtml[2]}.{$mHtml[1]}";
        }
        if (!preg_match('#^(0[1-9]|[12]\d|3[01])\.(0[1-9]|1[0-2])\.(\d{4})$#', $date, $m)) {
            throw new Exception('Дата должна быть в формате ДД.ММ.ГГГГ');
        }
        $isoDate = "{$m[3]}-{$m[2]}-{$m[1]}";   // для БД

        /* 3. Количество */
        if ($qty < 1) {
            throw new Exception('Количество должно быть положительным целым числом');
        }

        /* 4. Фото */
        $photo = null;
        if (isset($_FILES['photo']) && is_uploaded_file($_FILES['photo']['tmp_name'])) {
            $photo = file_get_contents($_FILES['photo']['tmp_name']);
        }

        /* ---------- CREATE ---------- */
        if ($postAction === 'create') {
            $sql = 'INSERT INTO Consumable
                    (name, description, arrival_date, image, quantity,
                     responsible_user_id, temporary_responsible_user_id,
                     consumable_type_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
            $pdo->prepare($sql)->execute([
                $name, $desc, $isoDate, $photo, $qty,
                $resp, $temp, $type
            ]);
            echo json_encode(['status' => 'success'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        /* ---------- UPDATE ---------- */
        if ($postAction === 'update') {
            $id = (int)($_POST['id'] ?? 0);

            // прошлый ответственный?
            $oldResp = (int)$pdo
                ->query('SELECT responsible_user_id FROM Consumable WHERE id=' . $id)
                ->fetchColumn();

            // формируем SET-часть
            $sets = [
                'name = :name',
                'description = :desc',
                'arrival_date = :date',
                'quantity = :qty',
                'responsible_user_id = :resp',
                'temporary_responsible_user_id = :temp',
                'consumable_type_id = :type'
            ];
            $params = [
                ':name' => $name,
                ':desc' => $desc,
                ':date' => $isoDate,
                ':qty'  => $qty,
                ':resp' => $resp,
                ':temp' => $temp,
                ':type' => $type,
                ':id'   => $id
            ];
            if ($photo !== null) {
                $sets[]            = 'image = :img';
                $params[':img'] = $photo;
            }
            $sql = 'UPDATE Consumable SET ' . implode(', ', $sets) . ' WHERE id = :id';
            $pdo->prepare($sql)->execute($params);

            // история, если изменился ответственный
            if ($oldResp !== $resp) {
                ConsumableResponsibleHistoryContext::add($id, $resp, $hcomm);
            }

            echo json_encode(['status' => 'success'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        throw new Exception('Unknown POST action');
    }

    throw new Exception('Invalid request method');
}
catch (Throwable $e) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
