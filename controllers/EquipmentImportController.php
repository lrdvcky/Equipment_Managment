<?php
// controllers/EquipmentImportController.php

ini_set('display_errors',1);
error_reporting(E_ALL);

require_once __DIR__ . '/../models/EquipmentContext.php';
require_once __DIR__ . '/../models/RoomContext.php';
require_once __DIR__ . '/../models/UsersContext.php';
require_once __DIR__ . '/../models/InventoryCheckContext.php';

/**
 * Читает .xlsx и возвращает массив строк.
 * Каждая строка — ассоц. массив вида ['A'=>'Принтер', 'B'=>'123', ...]
 */
function parseXlsx(string $file): array {
    $zip = new ZipArchive;
    if ($zip->open($file) !== true) {
        throw new Exception("Не удалось открыть XLSX как ZIP");
    }

    // 1) sharedStrings.xml (для строковых ячеек)
    $shared = [];
    if (($idx = $zip->locateName('xl/sharedStrings.xml')) !== false) {
        $xml = simplexml_load_string($zip->getFromIndex($idx));
        foreach ($xml->si as $si) {
            // может быть <t> или <r><t>
            if (isset($si->t)) {
                $shared[] = (string)$si->t;
            } else {
                $shared[] = (string)$si->r->t;
            }
        }
    }

    // 2) сам лист
    $sheetIdx = $zip->locateName('xl/worksheets/sheet1.xml');
    if ($sheetIdx === false) {
        throw new Exception("Не найден xl/worksheets/sheet1.xml");
    }
    $xml = simplexml_load_string($zip->getFromIndex($sheetIdx));

    $rows = [];
    foreach ($xml->sheetData->row as $row) {
        $r = [];
        foreach ($row->c as $c) {
            $ref = (string)$c['r'];          // e.g. "B2"
            $col = preg_replace('/[0-9]/','',$ref); 
            $val = null;
            // тип ячейки
            $t = (string)$c['t'];
            if (isset($c->v)) {
                $v = (string)$c->v;
                if ($t === 's') {
                    // shared string
                    $val = $shared[(int)$v] ?? null;
                } else {
                    $val = $v;
                }
            } elseif (isset($c->is->t)) {
                $val = (string)$c->is->t;
            }
            $r[$col] = $val;
        }
        $rows[] = $r;
    }

    $zip->close();
    return $rows;
}

// Обработка POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['import'])) {
    http_response_code(400);
    exit('Неверный запрос');
}

try {
    $xlsFile = $_FILES['import']['tmp_name'];
    $data    = parseXlsx($xlsFile);
    if (count($data) < 2) {
        throw new Exception("Файл пуст или в нём только заголовок");
    }

    // 0-я строка — заголовки
    $hdr = array_map('trim', $data[0]);
    // ищем буквы колонок по именам
    $map = array_flip($hdr);

    // обязательные
    foreach (['name','inventory_number','room','responsible_user','model_name','equipment_type','inventory_section'] as $col) {
        if (!isset($map[$col])) {
            throw new Exception("Не найден столбец «{$col}» в заголовке");
        }
    }

    $errors = [];
    // перебираем со второй строки
    for ($i = 1; $i < count($data); $i++) {
        $row = $data[$i];
        // пропускаем пустые
        if (empty(trim($row[$map['name']] ?? ''))) continue;

        try {
            // 1) готовим массив для create()
            $d = [
                'name'       => trim($row[$map['name']] ?? ''),
                'inventory_number' => (int)($row[$map['inventory_number']] ?? 0),
                'room_id'    => null,
                'responsible_user_id' => null,
                'temporary_responsible_user_id' => null,
                'price'      => is_numeric($row[$map['price']] ?? '') 
                                 ? $row[$map['price']] : null,
                'model_name' => trim($row[$map['model_name']] ?? ''),
                'direction_name' => trim($row[$map['direction_name']] ?? ''),
                'status'     => trim($row[$map['status']] ?? ''),
                'comment'    => trim($row[$map['comment']] ?? ''),
                'equipment_type'  => trim($row[$map['equipment_type']] ?? ''),
                'inventory_section' => null,
            ];

            // 2) Находим room_id
            $roomName = trim($row[$map['room']] ?? '');
            $room = RoomContext::findByName($roomName);
            if (!$room) throw new Exception("Строка ".($i+1).": аудитория «{$roomName}» не найдена");
            $d['room_id'] = $room->id;

            // 3) Находим ответственного
            $login = trim($row[$map['responsible_user']] ?? '');
            $u = UsersContext::findByUsername($login);
            if (!$u) throw new Exception("Строка ".($i+1).": пользователь «{$login}» не найден");
            $d['responsible_user_id'] = $u->id;

            // 4) Временный
            $tmpL = trim($row[$map['temporary_user']] ?? '');
            if ($tmpL !== '') {
                $u2 = UsersContext::findByUsername($tmpL);
                if (!$u2) throw new Exception("Строка ".($i+1).": временный пользователь «{$tmpL}» не найден");
                $d['temporary_responsible_user_id'] = $u2->id;
            }

            // 5) inventory_section
            $secName = trim($row[$map['inventory_section']] ?? '');
            $sec = InventoryCheckContext::findByName($secName);
            if (!$sec) throw new Exception("Строка ".($i+1).": раздел «{$secName}» не найден");
            $d['inventory_section'] = $sec->name;
            
            $d['photo'] = null;  // чтобы в EquipmentContext не было undefined key

try {
    // перед вставкой проверяем, не занят ли уже такой inventory_number
    $inv = $d['inventory_number'];
    if (\EquipmentContext::existsInventoryNumber($inv)) {
        throw new Exception("строка ".($i+1).": инв. номер {$inv} уже есть, пропускаем");
    }

    EquipmentContext::create($d);

} catch (Exception $eRow) {
    $errors[] = $eRow->getMessage();
}
            // 6) Создаём
            EquipmentContext::create($d);

        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }
    }

    if ($errors) {
        echo "Импорт завершён с ошибками:\n" . implode("\n", $errors);
    } else {
        echo "Импорт успешно завершён.";
    }

} catch (\Exception $e) {
    http_response_code(500);
    echo "Fatal: " . $e->getMessage();
}
