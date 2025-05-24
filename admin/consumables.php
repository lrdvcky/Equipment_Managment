<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Расходные материалы | Админ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <style>
    /* Модальное окно */
    .modal {
      display: none;
      position: fixed; top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.6);
      align-items: center; justify-content: center;
      z-index: 1000;
    }
    .modal-content {
      background: #FFF;
      border-radius: 8px;
      padding: 20px;
      width: 90%; max-width: 500px;
      position: relative;
    }
    .close-button {
      position: absolute; top: 10px; right: 10px;
      font-size: 24px; cursor: pointer; color: #666;
    }
    .close-button:hover { color: #000; }
    .modal-content form {
      display: grid;
      grid-gap: 10px;
    }
    .modal-content form label {
      display: flex;
      flex-direction: column;
      font-size: 14px; color: #333;
    }
    .modal-content form input,
    .modal-content form select,
    .modal-content form textarea {
      margin-top: 4px;
      padding: 8px;
      border: 1px solid #CCC;
      border-radius: 4px;
      font-size: 14px;
    }
    .modal-content form button {
      padding: 10px;
      background: #E53935;
      border: none; color: #FFF;
      border-radius: 4px;
      cursor: pointer;
      font-size: 15px;
    }
    .modal-content form button:hover {
      background: #D32F2F;
    }
    </style>
</head>
<body>
<div class="wrapper">

  <header>
    <div class="header-content">
      <img src="../img/logo.png" alt="Логотип" class="logo">
      <h1>Система учёта оборудования</h1>
      <a href="../logout.php" class="red-button" style="margin-bottom:10px;text-decoration:none;">Выход</a>
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
      <input id="search-input" type="text" placeholder="Поиск по наименованию...">
      <button id="add-btn" class="red-button">Добавить расходник</button>
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
            <th>Временно отв.</th>
            <th>Характеристики</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody id="consumables-body">
          <!-- JS подставит строки -->
        </tbody>
      </table>
    </div>

    <p style="margin-top:30px;font-size:14px;color:#555;">
      * Проверка: дата — ДД.MM.ГГГГ, количество — цифры.<br>
      * Удаление связанных данных сопровождается уведомлением.
    </p>
  </main>

  <footer>
    &copy; 2025 Учебное заведение. Все права защищены.
  </footer>
</div>

<!-- Модальное окно для add/edit -->
<div id="consumable-modal" class="modal">
  <div class="modal-content">
    <span class="close-button" onclick="closeModal()">&times;</span>
    <h3 id="modal-title">Добавить расходник</h3>
    <form id="consumable-form">
      <input type="hidden" id="consumable-id" name="id">
      
      <label>Наименование*:
        <input type="text" id="name" name="name" required>
      </label>
      
      <label>Описание:
        <textarea id="description" name="description"></textarea>
      </label>
      
      <label>Дата поступления*:
        <input type="date" id="arrival_date" name="arrival_date" required>
      </label>
      
      <label>Количество*:
        <input type="number" id="quantity" name="quantity" min="1" required>
      </label>
      
      <label>Тип*:
        <select id="consumable_type_id" name="consumable_type_id" required>
          <option value="">— Выберите тип —</option>
        </select>
      </label>
      
      <label>Ответственный*:
        <select id="responsible_user_id" name="responsible_user_id" required>
          <option value="">— Выберите —</option>
        </select>
      </label>
      
      <label>Временно отв.:
        <select id="temporary_responsible_user_id" name="temporary_responsible_user_id">
          <option value="">— Выберите —</option>
        </select>
      </label>
      
      <button type="submit">Сохранить</button>
    </form>
  </div>
</div>

<script>
  const API = '../controllers/ConsumableController.php';
  let consumables = [];
  let editingId = null;

  document.addEventListener('DOMContentLoaded', () => {
    loadTypes();
    loadUsers();
    fetchConsumables();

    document.getElementById('add-btn').addEventListener('click', openAddModal);
    document.getElementById('search-input').addEventListener('input', filterList);
    document.getElementById('consumable-form').addEventListener('submit', saveConsumable);
    document.getElementById('consumables-body').addEventListener('click', onTableClick);
  });

  async function loadTypes() {
    const res = await fetch(`${API}?action=getTypes`);
    const types = await res.json();
    const sel = document.getElementById('consumable_type_id');
    types.forEach(t => {
      sel.insertAdjacentHTML('beforeend',
        `<option value="${t.id}">${t.name}</option>`);
    });
  }

  async function loadUsers() {
    const res = await fetch(`${API}?action=getUsers`);
    const users = await res.json();
    const rsel = document.getElementById('responsible_user_id');
    const tsel = document.getElementById('temporary_responsible_user_id');
    users.forEach(u => {
      const opt = `<option value="${u.id}">${u.name}</option>`;
      rsel.insertAdjacentHTML('beforeend', opt);
      tsel.insertAdjacentHTML('beforeend', opt);
    });
  }

  async function fetchConsumables() {
    const res = await fetch(`${API}?action=get`);
    consumables = await res.json();
    renderList(consumables);
  }

  function renderList(arr) {
    const body = document.getElementById('consumables-body');
    body.innerHTML = arr.map(c => `
      <tr>
        <td>${c.id}</td>
        <td>${c.name}</td>
        <td>${c.description||''}</td>
        <td>${c.arrival_date||''}</td>
        <td>${c.quantity}</td>
        <td>${c.type_name}</td>
        <td>${c.responsible_name}</td>
        <td>${c.temporary_responsible_name||''}</td>
        <td>${c.properties||''}</td>
        <td>
          <button class="edit-btn" data-id="${c.id}">Изм.</button>
          <button class="delete-btn" data-id="${c.id}">Удал.</button>
        </td>
      </tr>
    `).join('');
  }

  function filterList() {
    const q = this.value.trim().toLowerCase();
    renderList(consumables
      .filter(c => c.name.toLowerCase().includes(q))
    );
  }

  function openAddModal() {
    editingId = null;
    document.getElementById('modal-title').textContent = 'Добавить расходник';
    document.getElementById('consumable-form').reset();
    document.getElementById('consumable-modal').style.display = 'flex';
  }

  function openEditModal(id) {
    editingId = id;
    const c = consumables.find(x => x.id == id);
    document.getElementById('modal-title').textContent = 'Редактировать расходник';
    document.getElementById('consumable-id').value = c.id;
    document.getElementById('name').value = c.name;
    document.getElementById('description').value = c.description;
    document.getElementById('arrival_date').value = c.arrival_date;
    document.getElementById('quantity').value = c.quantity;
    document.getElementById('consumable_type_id').value = c.consumable_type_id;
    document.getElementById('responsible_user_id').value = c.responsible_user_id;
    document.getElementById('temporary_responsible_user_id').value = c.temporary_responsible_user_id || '';
    document.getElementById('consumable-modal').style.display = 'flex';
  }

  function closeModal() {
    document.getElementById('consumable-modal').style.display = 'none';
  }
  window.addEventListener('click', e => {
    if (e.target.id === 'consumable-modal') closeModal();
  });

  async function saveConsumable(ev) {
    ev.preventDefault();
    const form = ev.target;
    const fd = new FormData(form);
    fd.set('action', editingId ? 'update' : 'create');
    if (editingId) fd.set('id', editingId);

    const res = await fetch(API, { method: 'POST', body: fd });
    const js = await res.json();
    if (js.status === 'success') {
      closeModal();
      fetchConsumables();
    } else {
      alert('Ошибка: ' + (js.message || res.statusText));
    }
  }

  async function deleteConsumable(id) {
    if (!confirm('Удалить расходник?')) return;
    const fd = new FormData();
    fd.set('action', 'destroy');
    fd.set('id', id);
    await fetch(API, { method: 'POST', body: fd });
    fetchConsumables();
  }

  function onTableClick(e) {
    if (e.target.classList.contains('edit-btn')) {
      openEditModal(e.target.dataset.id);
    }
    if (e.target.classList.contains('delete-btn')) {
      deleteConsumable(e.target.dataset.id);
    }
  }

  function toggleMenu() {
    document.getElementById('mobileMenu').classList.toggle('open');
  }
</script>
</body>
</html>
