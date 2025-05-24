<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Пользователи | Админ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <style>
    /* Модальное окно */
    .modal {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
    }
    .modal-content {
      background: #fff;
      padding: 20px;
      border-radius: 5px;
      max-width: 500px;
      width: 90%;
      position: relative;
    }
    .close-button {
      position: absolute;
      top: 10px; right: 10px;
      font-size: 24px;
      cursor: pointer;
    }
    .modal-content label {
      display: block;
      margin-bottom: 10px;
    }
    .modal-content input,
    .modal-content select {
      width: 100%;
      padding: 5px;
      margin-top: 5px;
      box-sizing: border-box;
    }
    </style>
</head>
<body>
<div class="wrapper">
  <header>
    <div class="header-content">
      <img src="../img/logo.png" alt="Логотип" class="logo">
      <h1>Система учёта оборудования</h1>
      <a href="../logout.php" class="red-button" style=" text-decoration: none;">Выход</a>
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
      <input type="text" id="search" placeholder="Поиск по фамилии, логину..." oninput="filterUsers()">
      <button class="red-button" onclick="addUser()">Добавить пользователя</button>
    </div>

    <div class="equipment-table">
      <table>
        <thead>
          <tr>
            <th>Логин</th><th>Роль</th><th>ФИО</th><th>Email</th><th>Телефон</th><th>Адрес</th><th>Действия</th>
          </tr>
        </thead>
        <tbody id="users-body"></tbody>
      </table>
    </div>
  </main>

  <footer>
    &copy; 2025 Учебное заведение. Все права защищены.
  </footer>
</div>

<!-- Модальное окно для создания/редактирования -->
<div id="user-modal" class="modal">
  <div class="modal-content">
    <span class="close-button" onclick="closeModal()">&times;</span>
    <h3 id="modal-title">Добавить пользователя</h3>
    <form id="user-form" novalidate>
      <input type="hidden" id="user-id" name="id">
      <label>Логин:<input type="text" id="username" name="username" required></label>
      <label id="label-password">Пароль:<input type="password" id="password" name="password" required></label>
      <label>Роль:
      <select id="role" name="role">
        <option value="admin">Администратор</option>
        <option value="teacher">Ответственное лицо</option>
        <option value="staff">Пользователь</option>
      </select>
      </label>
      <label>Email:<input type="email" id="email" name="email"></label>
      <label>Фамилия:<input type="text" id="last_name" name="last_name" required></label>
      <label>Имя:<input type="text" id="first_name" name="first_name" required></label>
      <label>Отчество:<input type="text" id="middle_name" name="middle_name"></label>
      <label>Телефон:<input type="text" id="phone" name="phone"></label>
      <label>Адрес:<input type="text" id="address" name="address"></label>
      <button type="submit" class="red-button">Сохранить</button>
    </form>
  </div>
</div>

<script>
let users = [];
let editingUserId = null;

// Загрузка списка
function fetchUsers() {
  fetch('../controllers/UserController.php?action=get')
    .then(res => res.json())
    .then(data => {
      users = data;
      renderUsers(users);
    })
    .catch(err => console.error('Fetch error:', err));
}

// Отрисовка
function renderUsers(list) {
  const tbody = document.getElementById('users-body');
  tbody.innerHTML = '';
  list.forEach(u => {
    tbody.innerHTML += `
      <tr>
        <td>${u.username}</td>
        <td>${u.role}</td>
        <td>${u.last_name} ${u.first_name}${u.middle_name ? ' ' + u.middle_name : ''}</td>
        <td>${u.email||''}</td>
        <td>${u.phone||''}</td>
        <td>${u.address||''}</td>
        <td>
          <button onclick="editUser(${u.id})">Изм.</button>
          <button onclick="deleteUser(${u.id})">Удал.</button>
        </td>
      </tr>`;
  });
}

// Фильтр по поиску
function filterUsers() {
  const q = document.getElementById('search').value.toLowerCase();
  renderUsers(users.filter(u =>
    u.username.toLowerCase().includes(q) ||
    u.last_name.toLowerCase().includes(q)
  ));
}

// Удаление
function deleteUser(id) {
  if (!confirm('Удалить этого пользователя?')) return;
  fetch('../controllers/UserController.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'delete', id })
  }).then(() => fetchUsers());
}

// Открыть форму создания
function addUser() {
  editingUserId = null;
  document.getElementById('modal-title').textContent = 'Добавить пользователя';
  document.getElementById('user-form').reset();
  document.getElementById('label-password').style.display = '';
  openModal();
}

// Открыть форму редактирования
function editUser(id) {
  editingUserId = id;
  const u = users.find(x => x.id === id);
  document.getElementById('modal-title').textContent = 'Редактировать пользователя';
  document.getElementById('user-id').value = u.id;
  document.getElementById('username').value = u.username;
  // пароль не показываем
  document.getElementById('label-password').style.display = 'none';
  document.getElementById('role').value = u.role;
  document.getElementById('email').value = u.email || '';
  document.getElementById('last_name').value = u.last_name;
  document.getElementById('first_name').value = u.first_name;
  document.getElementById('middle_name').value = u.middle_name || '';
  document.getElementById('phone').value = u.phone || '';
  document.getElementById('address').value = u.address || '';
  openModal();
}

function openModal() {
  document.getElementById('user-modal').style.display = 'flex';
}
function closeModal() {
  document.getElementById('user-modal').style.display = 'none';
}

// Сохранение формы
document.getElementById('user-form').addEventListener('submit', e => {
  e.preventDefault();
  const data = {
    username: document.getElementById('username').value,
    role: document.getElementById('role').value,
    email: document.getElementById('email').value,
    last_name: document.getElementById('last_name').value,
    first_name: document.getElementById('first_name').value,
    middle_name: document.getElementById('middle_name').value,
    phone: document.getElementById('phone').value,
    address: document.getElementById('address').value
  };
  if (!editingUserId) {
    data.password = document.getElementById('password').value;
  }
  const payload = editingUserId
    ? { action: 'update', id: editingUserId, data }
    : { action: 'create', data };

  fetch('../controllers/UserController.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  })
  .then(res => res.json())
  .then(resp => {
    if (resp.status === 'success') {
      closeModal();
      fetchUsers();
    } else {
      alert('Ошибка: ' + (resp.message || 'неизвестная'));
    }
  })
  .catch(err => alert('Сетевой ошибка: ' + err));
});

// Закрытие кликом вне окна
window.addEventListener('click', e => {
  if (e.target.id === 'user-modal') closeModal();
});

// Инициализация
document.addEventListener('DOMContentLoaded', fetchUsers);
</script>
</body>
</html>