<?php 
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Модули | Учёт оборудования</title>
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
        <a href="rooms.php">Аудитории</a>
        <a href="#">Программы</a>
        <a href="#">Инвентаризация</a>
        <a href="#">Расходники</a>
    </nav>

    <main>
        <h2 class="highlight">Выберите модуль</h2>
        <div class="modules-list">
            <a href="equipment.php" class="module-button">Оборудование</a>
            <a href="rooms.php" class="module-button">Аудитории</a>
            <a href="#" class="module-button">Пользователи</a>
            <a href="#" class="module-button">Программы</a>
            <a href="#" class="module-button">Инвентаризация</a>
            <a href="#" class="module-button">Расходные материалы</a>
        </div>
    </main>

    <footer>
        &copy; 2025 Учебное заведение. Все права защищены.
    </footer>

</div>

</body>
</html>
