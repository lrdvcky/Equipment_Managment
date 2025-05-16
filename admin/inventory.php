<?php 
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Инвентаризация | Учёт оборудования</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="wrapper">

    <header>
        <div class="header-content">
            <img src="../img/logo.png" alt="Логотип" class="logo">
            <h1>Система учёта оборудования</h1>
        </div>
    </header>

    <nav>
        <a href="index.php">Главная</a>
        <a href="equipment.php">Оборудование</a>
        <a href="users.php">Пользователи</a>
        <a href="rooms.php">Аудитории</a>
        <a href="software.php">Программы</a>
        <a href="inventory.php">Инвентаризация</a>
        <a href="consumables.php">Расходники</a>
        <a href="network.php">Сетевые настройки</a>
    </nav>

    <main>
        <h2 class="highlight">Инвентаризация оборудования</h2>

        <p>В этом разделе администратор может запускать новые инвентаризации, указывать дату, название, а также отслеживать результаты проверок, кто из пользователей проверял оборудование, с комментариями и статусами.</p>

        <div class="equipment-controls">
            <input type="text" placeholder="Поиск по названию инвентаризации">
            <button class="red-button">Добавить инвентаризацию</button>
        </div>

        <h3>Список инвентаризаций</h3>
        <div class="equipment-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Наименование</th>
                        <th>Дата начала</th>
                        <th>Дата окончания</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Инвентаризация весна 2025</td>
                        <td>01.03.2025</td>
                        <td>10.03.2025</td>
                        <td class="table-actions">
                            <a href="#">Оборудование</a>
                            <a href="#">Удалить</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h3>Результаты инвентаризации №1</h3>
        <div class="equipment-table">
            <table>
                <thead>
                    <tr>
                        <th>Оборудование</th>
                        <th>Пользователь</th>
                        <th>Комментарий</th>
                        <th>Статус</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Монитор Samsung</td>
                        <td>Басалаев А.И.</td>
                        <td>Оборудование в порядке</td>
                        <td><span style="color: green;">Принято</span></td>
                    </tr>
                    <tr>
                        <td>ПК Lenovo</td>
                        <td>Суслонова М.Л.</td>
                        <td>Всё функционирует</td>
                        <td><span style="color: green;">Принято</span></td>
                    </tr>
                    <tr>
                        <td>Принтер HP</td>
                        <td>Субботина Ю.А.</td>
                        <td>Устройство на ремонте</td>
                        <td><span style="color: red;">Не принято</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

    </main>

    <footer>
        &copy; 2025 Учебное заведение. Все права защищены.
    </footer>

</div>

</body>
</html>
