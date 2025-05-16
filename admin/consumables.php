<?php 
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Расходники | Учёт оборудования</title>
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
        <a href="rooms.php">Аудитории</a>
        <a href="software.php">Программы</a>
        <a href="inventory.php">Инвентаризация</a>
        <a href="consumables.php">Расходники</a>
        <a href="network.php">Сетевые настройки</a>
    </nav>

    <main>
        <h2 class="highlight">Расходные материалы</h2>

        <div class="equipment-controls">
            <input type="text" placeholder="Поиск по названию или типу">
            <button class="red-button">Добавить расходник</button>
        </div>

        <div class="equipment-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Наименование</th>
                        <th>Тип</th>
                        <th>Количество</th>
                        <th>Дата поступления</th>
                        <th>Ответственный</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Картридж HP 106A</td>
                        <td>Картридж</td>
                        <td>5</td>
                        <td>01.04.2025</td>
                        <td>Басалаев А.И.</td>
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
