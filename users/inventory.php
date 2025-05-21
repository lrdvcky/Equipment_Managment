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
                <tbody id="inventory-body"></tbody>
            </table>
        </div>
        <br>
        <button type="submit" id="submit-button" class="red-button">Отправить</button>
    </main>

    <footer>
        &copy; 2025 Учебное заведение. Все права защищены.
    </footer>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
    fetch('../user_controller/EquipmentInventoryCheckUserController.php')
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('inventory-body');
            tbody.innerHTML = data.map(item => `
                <tr>
                    <td>${item.name}</td>
                    <td><input type="text" name="comment" data-id="${item.equipment_id}" placeholder="Комментарий..."></td>
                    <td><input type="checkbox" name="check" data-id="${item.equipment_id}"></td>
                </tr>
            `).join('');
        });
});

document.getElementById('inventory-form').addEventListener('submit', e => {
    e.preventDefault();
    const rows = document.querySelectorAll('#inventory-body tr');
    rows.forEach(row => {
        const equipment_id = row.querySelector('input[name="comment"]').dataset.id;
        const comment = row.querySelector('input[name="comment"]').value;
        const check = row.querySelector('input[name="check"]').checked;

        fetch('../user_controller/EquipmentInventoryCheckUserController.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({equipment_id, comment, inventory_check_id: 1, check})
        });
    });
    alert("Данные успешно отправлены");
});
    function toggleMenu() {
        const nav = document.getElementById('mobileMenu');
        nav.classList.toggle('open');
    }
</script>
</body>
</html>
