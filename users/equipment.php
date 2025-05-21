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
                <tbody id="equipment-body">
    <!-- Данные будут загружены динамически -->
</tbody>

            </table>
        </div>
    </main>

    <footer>
        &copy; 2025 Учебное заведение. Все права защищены.
    </footer>
</div>
<script>
    document.getElementById('submit-button').addEventListener('click', function() {
    const comment = document.getElementById('comment').value;
    const check = document.getElementById('status').checked;
    const equipmentId = /* Получите ID оборудования, например, из выбранного элемента или другого источника */;

    fetch('../user_controller/EquipmentInventoryCheckUserController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            equipment_id: equipmentId,
            comment: comment,
            check: check
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Данные успешно обновлены.');
            // Дополнительно можно обновить интерфейс, например, изменить статус на "Принято"
        } else {
            alert('Ошибка при обновлении данных.');
        }
    })
    .catch(error => {
        console.error('Ошибка:', error);
        alert('Произошла ошибка при отправке данных.');
    });
});

    function toggleMenu() {
        const nav = document.getElementById('mobileMenu');
        nav.classList.toggle('open');
    }
</script>
</body>
</html>
