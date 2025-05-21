<?php
require_once __DIR__ . '/../connection.php';

class ConsumableTypeContext
{
    /**
     * Возвращает все записи из таблицы ConsumableType
     * @return array of objects {id:int, name:string}
     */
    public static function getAll(): array
    {
        $db = Connection::getInstance();
        $stmt = $db->query("SELECT id, name FROM ConsumableType ORDER BY name");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $out = [];
        foreach ($rows as $row) {
            $item = new stdClass;
            $item->id   = (int)$row['id'];
            $item->name = $row['name'];
            $out[]      = $item;
        }
        return $out;
    }
}
