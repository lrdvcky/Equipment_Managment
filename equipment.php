<?php 
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оборудование | Учёт оборудования</title>
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
        <a href="equipment.php" class="active">Оборудование</a>
        <a href="#">Пользователи</a>
        <a href="#">Программы</a>
        <a href="#">Инвентаризация</a>
        <a href="#">Расходники</a>
    </nav>

    <main>
        <h2 class="highlight">Учёт оборудования</h2>

        <div class="equipment-controls">
            <input type="text" placeholder="Поиск по наименованию или инвентарному номеру">
            <button class="red-button">Добавить оборудование</button>
        </div>

        <div class="equipment-table">
            <table>
                <thead>
    <tr>
        <th>ID</th>
        <th>Фото</th>
        <th>Наименование</th>
        <th>Инв. №</th>
        <th>Аудитория</th>
        <th>Статус</th>
        <th>Модель</th>
        <th>Ответственный</th>
        <th>Действия</th>
    </tr>
</thead>
<tbody>
    <tr>
        <td>4</td>
        <td><img src="img/equipment-placeholder.jpg" alt="Фото" class="equipment-photo"></td>
        <td>Монитор Samsung</td>
        <td>INV001</td>
        <td>Ауд. 422</td>
        <td>В эксплуатации</td>
        <td>S24F350</td>
        <td>Иванов И.И.</td>
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
