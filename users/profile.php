<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
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
            <a href="../logout.php" class="red-button" style="margin-bottom: 10px; text-decoration: none;">Выход</a>
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

        <ul id="profile-data">
            <li><strong>ФИО:</strong> <span id="full_name"></span></li>
            <li><strong>Логин:</strong> <span id="username"></span></li>
            <li><strong>Email:</strong> <span id="email"></span></li>
            <li><strong>Телефон:</strong> <span id="phone"></span></li>
            <li><strong>Адрес:</strong> <span id="address"></span></li>
            <li><strong>Роль:</strong> <span id="role"></span></li>
        </ul>
    </main>

    <footer>
        &copy; 2025 Учебное заведение. Все права защищены.
    </footer>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        fetch('../user_controller/UserProfileController.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    document.getElementById('full_name').textContent = data.full_name;
                    document.getElementById('username').textContent = data.username;
                    document.getElementById('email').textContent = data.email;
                    document.getElementById('phone').textContent = data.phone;
                    document.getElementById('address').textContent = data.address;
                    document.getElementById('role').textContent = data.role;
                }
            })
            .catch(error => {
                console.error('Ошибка при получении данных профиля:', error);
            });
    });
    function toggleMenu() {
        const nav = document.getElementById('mobileMenu');
        nav.classList.toggle('open');
    }
</script>
</body>
</html>
