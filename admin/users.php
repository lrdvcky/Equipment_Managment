<?php
require_once '../connection.php';
require_once '../models/User.php';
require_once '../models/UsersContext.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                UsersContext::addUser(new Users(
                    0, $_POST['username'], $_POST['password'], $_POST['role'],
                    $_POST['email'], $_POST['last_name'], $_POST['first_name'],
                    $_POST['middle_name'], $_POST['phone'], $_POST['address']
                ));
                break;
            case 'update':
                UsersContext::updateUser(new Users(
                    $_POST['id'], $_POST['username'], $_POST['password'], $_POST['role'],
                    $_POST['email'], $_POST['last_name'], $_POST['first_name'],
                    $_POST['middle_name'], $_POST['phone'], $_POST['address']
                ));
                break;
            case 'delete':
                UsersContext::deleteUser($_POST['id']);
                break;
        }
        header('Location: users.php');
        exit();
    }
}

$users = UsersContext::getAllUsers();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Пользователи | Админ</title>
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
        <h2 class="highlight">Список пользователей</h2>

        <div class="equipment-controls">
            <input type="text" placeholder="Поиск по фамилии, логину...">
            <button class="red-button">Добавить пользователя</button>
        </div>

        <div class="equipment-table">
            <table>
                <thead>
                    <tr>
                        <th>Логин</th>
                        <th>Роль</th>
                        <th>ФИО</th>
                        <th>Email</th>
                        <th>Телефон</th>
                        <th>Адрес</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
<tr>
    <td><?= htmlspecialchars($user->username) ?></td>
    <td><?= htmlspecialchars($user->role) ?></td>
    <td><?= htmlspecialchars($user->last_name . ' ' . $user->first_name . ' ' . $user->middle_name) ?></td>
    <td><?= htmlspecialchars($user->email) ?></td>
    <td><?= htmlspecialchars($user->phone) ?></td>
    <td><?= htmlspecialchars($user->address) ?></td>
    <td class="table-actions">
        <a href="#">Редактировать</a>
        <a href="#">Удалить</a>
    </td>
</tr>
<?php endforeach; ?>

                </tbody>
            </table>
        </div>

        <p style="margin-top: 30px; font-size: 14px; color: #555;">
            * Обязательные поля при добавлении: логин, пароль, фамилия.<br>
            * Проверка: поля должны быть валидны (например, телефон — только цифры).<br>
            * Нельзя удалить пользователя, связанного с другими модулями — система выдаст предупреждение.
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
