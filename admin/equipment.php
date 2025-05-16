<?php ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оборудование | Админ</title>
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
        <h2 class="highlight">Список оборудования</h2>

        <div class="equipment-controls">
            <input type="text" placeholder="Поиск по названию...">
            <button class="red-button">Добавить оборудование</button>
        </div>

        <div class="equipment-table">
            <table>
                <thead>
                    <tr>
                        <th>Фото</th>
                        <th>Название</th>
                        <th>Инв. номер</th>
                        <th>Аудитория</th>
                        <th>Ответственный</th>
                        <th>Вр. ответственный</th>
                        <th>Стоимость</th>
                        <th>Направление</th>
                        <th>Статус</th>
                        <th>Модель</th>
                        <th>Комментарий</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><img src="../img/sample.png" alt="фото" style="height: 40px;"></td>
                        <td>ПК Lenovo</td>
                        <td>INV002</td>
                        <td>Ауд.502</td>
                        <td>Суслонова</td>
                        <td>Субботина</td>
                        <td>35000 ₽</td>
                        <td>Программирование</td>
                        <td>Используется</td>
                        <td>Lenovo M720</td>
                        <td>Основной ПК</td>
                        <td class="table-actions">
                            <a href="#">Редактировать</a>
                            <a href="#">Удалить</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p style="margin-top: 30px; font-size: 14px; color: #555;">
            * При удалении оборудования, связанного с другими модулями, будет показано предупреждение.<br>
            * Инвентарный номер и название обязательны. Проверка: только цифры в номере, только цифры в стоимости.
        </p>
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
