<?php 
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Модули | Учёт оборудования</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="wrapper">

    <header>
    <div class="header-content">
        <img src="../img/logo.png" alt="Логотип" class="logo">
        <h1>Система учёта оборудования</h1>
        <button class="burger" onclick="toggleMenu()">☰</button>
    </div>
    <nav id="mobileMenu">
        <a href="index.php">Главная</a>
        <a href="equipment.php">Оборудование</a>
        <a href="users.php">Пользователи</a>
        <a href="rooms.php">Аудитории</a>
        <a href="software.php">Программы</a>
        <a href="inventory.php">Инвентаризация</a>
        <a href="consumables.php">Расходники</a>
        <a href="network.php">Сетевые настройки</a>
    </nav>
</header>
    <main>
        <h2 class="highlight">Выберите модуль</h2>
        <div class="modules-list">
            <a href="equipment.php" class="module-button">Оборудование</a>
            <a href="rooms.php" class="module-button">Аудитории</a>
            <a href="users.php" class="module-button">Пользователи</a>
            <a href="software.php" class="module-button">Программы</a>
            <a href="inventory.php" class="module-button">Инвентаризация</a>
            <a href="consumable.php" class="module-button">Расходные материалы</a>
            <a href="network.php" class="module-button">Сетевые настройки</a>

        </div>
    </main>

    <footer>
        &copy; 2025 Учебное заведение. Все права защищены.
    </footer>

</div>
<script>
    function toggleMenu() {
        const nav = document.getElementById('mobileMenu');
        nav.classList.toggle('open');
    }
</script>
</body>
</html>
