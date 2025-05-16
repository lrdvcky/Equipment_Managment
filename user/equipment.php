<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Моё оборудование</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="wrapper">

    <header>
        <div class="header-content">
            <img src="../img/logo.png" alt="Логотип" class="logo">
            <h1>Мое оборудование</h1>
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
        <h2 class="highlight">Закреплённое за вами оборудование</h2>
        <div class="equipment-table">
            <table>
                <thead>
                    <tr>
                        <th>Наименование</th>
                        <th>Инвентарный №</th>
                        <th>Аудитория</th>
                        <th>Комментарий</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>ПК Lenovo</td>
                        <td>INV002</td>
                        <td>Ауд.502</td>
                        <td>Рабочее место преподавателя</td>
                    </tr>
                </tbody>
            </table>
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
