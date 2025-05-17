<?php 

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Инвентаризация</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="wrapper">

    <header>
        <div class="header-content">
            <img src="../img/logo.png" alt="Логотип" class="logo">
            <h1>Инвентаризация</h1>
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
        <h2 class="highlight">Инвентаризация оборудования</h2>
        <p>Пожалуйста, отметьте проверенное оборудование и при необходимости оставьте комментарий.</p>

        <div class="equipment-table">
            <table>
                <thead>
                    <tr>
                        <th>Оборудование</th>
                        <th>Комментарий</th>
                        <th>Статус</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>ПК Lenovo</td>
                        <td><input type="text" placeholder="Комментарий..."></td>
                        <td><input type="checkbox"> Принято</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br>
        <button class="red-button">Отправить</button>
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
