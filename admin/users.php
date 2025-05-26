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
    /* Модалка */
    .modal {
      display: none;
      position: fixed; top: 0; left: 0;
      width:100%; height:100%;
      background: rgba(0,0,0,0.5);
      justify-content: center; align-items: center;
      z-index: 1000;
    }
    .modal-content {
      background: #fff;
      padding: 20px;
      border-radius: 5px;
      max-width: 500px; width: 90%;
      position: relative;
    }
    .close-button {
      position: absolute; top: 10px; right: 10px;
      font-size: 24px; cursor: pointer;
    }
    .modal-content label {
      display: block; margin-bottom: 10px;
      font-size: 14px;
    }
    .modal-content input,
    .modal-content select {
      width: 100%; padding: 6px; margin-top: 4px;
      box-sizing: border-box;
      border: 1px solid #CCC; border-radius: 4px;
    }
    table {
      width:100%; border-collapse: collapse; margin-bottom: 20px;
    }
    th, td {
      border:1px solid #DDD; padding: 8px 10px;
      font-size: 14px; text-align: left;
    }
    th {
      background: #F5F5F5; cursor: pointer;
    }
    tr.selected { background: #eef; }
  </style>
</head>
<body>
<div class="wrapper">
  <header>
    <div class="header-content">
      <img src="../img/logo.png" class="logo" alt="Логотип">
      <h1>Система учёта оборудования</h1>
      <a href="../logout.php" class="red-button" style="text-decoration:none;">Выход</a>
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

    <table>
      <thead>
        <tr>
          <th data-field="username">Логин ▲▼</th>
          <th data-field="password">Пароль ▲▼</th>
          <th data-field="role">Роль ▲▼</th>
          <th data-field="fio">ФИО ▲▼</th>
          <th data-field="email">Email ▲▼</th>
          <th data-field="phone">Телефон ▲▼</th>
          <th data-field="address">Адрес ▲▼</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody id="users-body"></tbody>
    </table>
  </main>

  <footer>
    &copy; 2025 Учебное заведение. Все права защищены.
  </footer>
</div>

<!-- Модалка добавления/редактирования -->
<div id="user-modal" class="modal">
  <div class="modal-content">
    <span class="close-button" onclick="closeModal()">&times;</span>
    <h3 id="modal-title">Добавить пользователя</h3>
    <form id="user-form" novalidate>
      <input type="hidden" id="user-id" name="id">

      <label>Логин*:
        <input type="text" id="username" name="username" required>
      </label>

      <label id="label-password">Пароль*:
        <input type="password" id="password" name="password" required>
      </label>

      <label>Роль*:
        <select id="role" name="role" required>
          <option value="">—Выберите—</option>
          <option value="admin">Администратор</option>
          <option value="teacher">Ответственное лицо</option>
          <option value="staff">Пользователь</option>
        </select>
      </label>

      <label>Email*:
        <input type="email" id="email" name="email" required>
      </label>

      <label>Фамилия*:
        <input type="text" id="last_name" name="last_name" required>
      </label>

      <label>Имя*:
        <input type="text" id="first_name" name="first_name" required>
      </label>

      <label>Отчество:
        <input type="text" id="middle_name" name="middle_name">
      </label>

      <label>Телефон:
        <input type="text" id="phone" name="phone">
      </label>

      <label>Адрес:
        <input type="text" id="address" name="address">
      </label>

      <button type="submit" class="red-button">Сохранить</button>
    </form>
  </div>
</div>

<script>
  let users = [];
  let editingUserId = null;
  let sortField = null, sortAsc = true;

  document.addEventListener('DOMContentLoaded', () => {
    fetchUsers();
    document.querySelectorAll('th[data-field]').forEach(th => {
      th.addEventListener('click', () => sortBy(th.dataset.field));
    });
    document.getElementById('user-form').addEventListener('submit', saveUser);
    window.addEventListener('click', e => {
      if (e.target.id === 'user-modal') closeModal();
    });
  });

  function fetchUsers() {
    fetch('../controllers/UserController.php?action=get')
      .then(r => r.json())
      .then(data => {
        users = data.map(u => ({
          ...u,
          fio: `${u.last_name} ${u.first_name}${u.middle_name ? ' '+u.middle_name : ''}`
        }));
        renderUsers(users);
      });
  }

  function renderUsers(list) {
    const tbody = document.getElementById('users-body');
    tbody.innerHTML = '';
    list.forEach(u => {
      tbody.insertAdjacentHTML('beforeend', `
        <tr ${u.id===editingUserId?'class="selected"':''}>
          <td>${u.username}</td>
          <td>${u.password}</td>
          <td>${u.role}</td>
          <td>${u.fio}</td>
          <td>${u.email}</td>
          <td>${u.phone||''}</td>
          <td>${u.address||''}</td>
          <td>
            <button onclick="event.stopPropagation(); editUser(${u.id})">Изм.</button>
            <button onclick="event.stopPropagation(); deleteUser(${u.id})">Удал.</button>
          </td>
        </tr>`);
    });
  }

  function filterUsers() {
    const q = document.getElementById('search').value.toLowerCase();
    renderUsers(users.filter(u =>
      u.username.toLowerCase().includes(q) ||
      u.fio.toLowerCase().includes(q) ||
      u.password.toLowerCase().includes(q)
    ));
  }

  function sortBy(field) {
    if (sortField === field) sortAsc = !sortAsc;
    else { sortField = field; sortAsc = true; }
    users.sort((a,b) => {
      if (a[field] < b[field]) return sortAsc ? -1 : 1;
      if (a[field] > b[field]) return sortAsc ? 1 : -1;
      return 0;
    });
    renderUsers(users);
  }

  function deleteUser(id) {
    if (!confirm('Удалить этого пользователя?')) return;
    fetch('../controllers/UserController.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({action:'delete',id})
    }).then(fetchUsers);
  }

  function addUser() {
    editingUserId = null;
    document.getElementById('modal-title').textContent = 'Добавить пользователя';
    document.getElementById('user-form').reset();
    document.getElementById('label-password').style.display = '';
    document.getElementById('password').required = true;
    openModal();
  }

  function editUser(id) {
    editingUserId = id;
    const u = users.find(x=>x.id===id);
    document.getElementById('modal-title').textContent = 'Редактировать пользователя';
    document.getElementById('user-id').value        = u.id;
    document.getElementById('username').value       = u.username;
    document.getElementById('password').value       = '';
    document.getElementById('label-password').style.display = '';
    document.getElementById('password').required    = false;
    document.getElementById('role').value           = u.role;
    document.getElementById('email').value          = u.email;
    document.getElementById('last_name').value      = u.last_name;
    document.getElementById('first_name').value     = u.first_name;
    document.getElementById('middle_name').value    = u.middle_name||'';
    document.getElementById('phone').value          = u.phone||'';
    document.getElementById('address').value        = u.address||'';
    openModal();
  }

  function openModal(){ document.getElementById('user-modal').style.display = 'flex'; }
  function closeModal(){ document.getElementById('user-modal').style.display = 'none'; }

  function saveUser(ev) {
    ev.preventDefault();
    const f = document.getElementById('user-form');
    if (!f.checkValidity()) { f.reportValidity(); return; }

    const payload = { action: editingUserId ? 'update' : 'create' };
    if (editingUserId) {
      payload.id   = editingUserId;
      payload.data = {
        username: f.username.value,
        role:     f.role.value,
        email:    f.email.value,
        last_name:   f.last_name.value,
        first_name:  f.first_name.value,
        middle_name: f.middle_name.value,
        phone:       f.phone.value,
        address:     f.address.value
      };
      if (f.password.value) payload.data.password = f.password.value;
    } else {
      payload.data = {
        username:    f.username.value,
        password:    f.password.value,
        role:        f.role.value,
        email:       f.email.value,
        last_name:   f.last_name.value,
        first_name:  f.first_name.value,
        middle_name: f.middle_name.value,
        phone:       f.phone.value,
        address:     f.address.value
      };
    }

    fetch('../controllers/UserController.php', {
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body: JSON.stringify(payload)
    })
    .then(r=>r.json())
    .then(res=>{
      if (res.status==='success') {
        closeModal();
        fetchUsers();
      } else alert('Ошибка: '+(res.message||'неизвестная'));
    })
    .catch(err=>alert('Сетевая ошибка: '+err));
  }
</script>
</body>
</html>
