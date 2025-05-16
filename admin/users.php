<?php 
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Пользователи | Учёт оборудования</title>
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
        <h2 class="highlight">Пользователи системы</h2>

        <div class="equipment-controls">
            <input type="text" placeholder="Поиск по фамилии, роли или логину">
            <button class="red-button">Добавить пользователя</button>
        </div>

        <div class="equipment-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Логин</th>
                        <th>Роль</th>
                        <th>ФИО</th>
                        <th>Телефон</th>
                        <th>Email</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Пример строки -->
                    <tr>
                        <td>1</td>
                        <td>basalaev</td>
                        <td>Администратор</td>
                        <td>Басалаев Александр Иванович</td>
                        <td>89123456789</td>
                        <td>ivanov@mail.ru</td>
                        <td><a href="#">Редактировать</a> | <a href="#">Удалить</a></td>
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
