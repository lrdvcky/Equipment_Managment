<?php 
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Инвентаризация | Учёт оборудования</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="wrapper">

    <header>
        <div class="header-content">
            <img src="img/logo.png" alt="Логотип" class="logo">
            <h1>Система учёта оборудования</h1>
        </div>
    </header>

    <nav>
        <a href="index.php">Главная</a>
        <a href="equipment.php">Оборудование</a>
        <a href="users.php">Пользователи</a>
        <a href="software.php">Программы</a>
        <a href="inventory.php" class="active">Инвентаризация</a>
        <a href="consumables.php">Расходники</a>
        <a href="rooms.php">Аудитории</a>
    </nav>

    <main>
        <h2 class="highlight">Инвентаризация оборудования</h2>

        <div class="equipment-controls">
            <input type="text" placeholder="Поиск по названию или дате">
            <button class="red-button">Добавить инвентаризацию</button>
        </div>

        <div class="equipment-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Наименование</th>
                        <th>Начало</th>
                        <th>Окончание</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Инвентаризация весна 2025</td>
                        <td>01.03.2025</td>
                        <td>10.03.2025</td>
                        <td class="table-actions">
                            <a href="#">Просмотр</a>
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
