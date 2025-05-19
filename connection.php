<?php
function OpenConnection() {
    $host = '127.0.0.1';
    $dbname = 'equipment_managment';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=127.0.0.1;port=3307;dbname=$dbname;charset=utf8mb4", $username, $password);

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Ошибка подключения к базе данных: " . $e->getMessage());
    }
}
?>
