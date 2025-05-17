<?php 

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Профиль</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="wrapper">

    <header>
        <div class="header-content">
            <img src="../img/logo.png" alt="Логотип" class="logo">
            <h1>Профиль пользователя</h1>
            <button class="burger" onclick="toggleMenu()">☰</button>
        </div>

    <nav id="mobileMenu">
        <a href="index.php">Главная</a>
        <a href="equipment.php">Моё оборудование</a>
        <a href="inventory.php">Инвентаризация</a>
        <a href="profile.php" class="active">Профиль</a>
    </nav>
</header>

    <main>
        <h2 class="highlight">Ваши данные</h2>

        <ul>
            <li><strong>ФИО:</strong> Суслонова Мария Лазаревна</li>
            <li><strong>Логин:</strong> suslonova</li>
            <li><strong>Email:</strong> petrova@mail.ru</li>
            <li><strong>Телефон:</strong> 89234567890</li>
            <li><strong>Адрес:</strong> г. Пермь, ул. Ленина, д.10</li>
            <li><strong>Роль:</strong> Преподаватель</li>
        </ul>
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
