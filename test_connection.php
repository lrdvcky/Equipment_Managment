<?php
require_once __DIR__ . '/connection.php';

try {
    $pdo = OpenConnection();
    echo "✔ Подключение к БД установлено<br>";

    // Выведем список таблиц в этой базе
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_NUM);
    echo "<pre>Таблицы в базе:\n" . print_r($tables, true) . "</pre>";
} catch (PDOException $e) {
    die("✘ Ошибка подключения: " . $e->getMessage());
}
?>