<?php ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Сетевые настройки | Учёт оборудования</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="wrapper">

    <header>
        <div class="header-content">
            <img src="../img/logo.png" alt="Логотип" class="logo">
            <h1>Система учёта оборудования</h1>
        </div>
    </header>

    <nav>
        <a href="index.php">Главная</a>
        <a href="equipment.php">Оборудование</a>
        <a href="users.php">Пользователи</a>
        <a href="software.php">Программы</a>
        <a href="inventory.php">Инвентаризация</a>
        <a href="consumables.php">Расходники</a>
        <a href="rooms.php">Аудитории</a>
        <a href="network.php">Сетевые настройки</a>
    </nav>

    <main>
        <h2 class="highlight">Настройки сети</h2>

        <div class="equipment-controls">
            <input type="text" placeholder="Поиск по IP или оборудованию">
            <button class="red-button">Добавить настройки</button>
        </div>

        <div class="equipment-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Оборудование</th>
                        <th>IP-адрес</th>
                        <th>Маска подсети</th>
                        <th>Шлюз</th>
                        <th>DNS-серверы</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Монитор Samsung</td>
                        <td>192.168.0.101</td>
                        <td>255.255.255.0</td>
                        <td>192.168.0.1</td>
                        <td>8.8.8.8, 8.8.4.4</td>
                        <td class="table-actions">
                            <a href="#">Редактировать</a>
                            <a href="#">Удалить</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        &copy; 2025 Учебное заведение. Все права защищены.
    </footer>

</div>

</body>
</html>
