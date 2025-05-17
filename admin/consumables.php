<?php 

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Расходные материалы | Админ</title>
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
        <h2 class="highlight">Учёт расходных материалов</h2>

        <div class="equipment-controls">
            <input type="text" placeholder="Поиск по наименованию...">
            <button class="red-button">Добавить расходник</button>
        </div>

        <div class="equipment-table">
            <table>
                <thead>
                    <tr>
                        <th>Фото</th>
                        <th>Наименование</th>
                        <th>Описание</th>
                        <th>Дата поступления</th>
                        <th>Количество</th>
                        <th>Тип</th>
                        <th>Ответственный</th>
                        <th>Временно ответственный</th>
                        <th>Характеристики</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><img src="../img/sample.png" alt="img" style="height: 40px;"></td>
                        <td>Картридж HP 106A</td>
                        <td>Чёрный лазерный картридж</td>
                        <td>01.04.2025</td>
                        <td>5</td>
                        <td>Картридж</td>
                        <td>Басалаев</td>
                        <td>Суслонова</td>
                        <td>Цвет: Чёрный; Объём: 1500 стр.</td>
                        <td class="table-actions">
                            <a href="#">Редактировать</a>
                            <a href="#">Удалить</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p style="margin-top: 30px; font-size: 14px; color: #555;">
            * Проверка: дата поступления в формате ДД.ММ.ГГГГ, количество — только цифры.<br>
            * Связанные с оборудованием расходники отображаются в карточке оборудования.<br>
            * Удаление связанных расходников и характеристик сопровождается уведомлением.
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
