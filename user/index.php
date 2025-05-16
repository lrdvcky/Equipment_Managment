<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Главная | Пользователь</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="wrapper">

    <header>
        <div class="header-content">
            <img src="../img/logo.png" alt="Логотип" class="logo">
            <h1>Главная</h1>
            <button class="burger" onclick="toggleMenu()">☰</button>
        </div>

    <nav id="mobileMenu">
        <a href="index.php">Главная</a>
        <a href="equipment.php">Моё оборудование</a>
        <a href="inventory.php">Инвентаризация</a>
        <a href="profile.php" class="active">Профиль</a>
    </nav>
</header>

    <main style="text-align: center;">
        <h2 class="highlight">Добро пожаловать!</h2>
        <p>Вы вошли как преподаватель или сотрудник. Используйте меню выше для управления своим оборудованием и участия в инвентаризациях.</p>
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
