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
            <input type="text" id="search" placeholder="Поиск по фамилии, логину...">
            <button class="red-button" onclick="addUser()">Добавить пользователя</button>
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
                <tbody id="users-body">
                    <!-- Данные вставятся через JS -->
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        &copy; 2025 Учебное заведение. Все права защищены.
    </footer>
</div>

<script>
function fetchUsers() {
    fetch('../controllers/UserController.php?action=get')
        .then(res => res.json())
        .then(users => {
            const tbody = document.getElementById('users-body');
            tbody.innerHTML = '';
            users.forEach(user => {
                tbody.innerHTML += `
                <tr>
                    <td>${user.username}</td>
                    <td>${user.role}</td>
                    <td>${user.last_name} ${user.first_name}${user.middle_name ? ' ' + user.middle_name : ''}</td>
                    <td>${user.email || ''}</td>
                    <td>${user.phone || ''}</td>
                    <td>${user.address || ''}</td>
                    <td>
                        <button onclick="editUser(${user.id})">Изм.</button>
                        <button onclick="deleteUser(${user.id})">Удал.</button>
                    </td>
                </tr>`;
            });
        })
        .catch(err => console.error('Failed to fetch users:', err));
}

function deleteUser(id) {
    const data = { action: 'delete', id };
    fetch('../controllers/UserController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    }).then(() => fetchUsers());
}

function editUser(id) {
    alert("Редактирование пока не реализовано");
}

document.addEventListener('DOMContentLoaded', fetchUsers);
</script>
</body>
</html>
