<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Оборудование | Админ</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../style.css">
  <style>
    /* --- Модалка --- */
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
      width: 90%; max-width: 600px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.2);
      position: relative;
    }
    .close-button {
      position: absolute; top: 10px; right: 10px;
      font-size: 24px; cursor: pointer; color: #666;
    }
    .close-button:hover { color: #000; }

    /* --- Форма в модалке (grid) --- */
    .modal-content form {
      display: grid;
      grid-template-columns: 1fr 1fr;
      grid-gap: 15px 20px;
    }
    .modal-content form label {
      display: flex;
      flex-direction: column;
      font-size: 14px;
      color: #333;
    }
    .modal-content form label.full {
      grid-column: 1 / -1;
    }
    .modal-content form input[type="text"],
    .modal-content form input[type="file"],
    .modal-content form input[type="number"],
    .modal-content form select,
    .modal-content form textarea {
      margin-top: 6px;
      padding: 8px;
      border: 1px solid #CCC;
      border-radius: 4px;
      font-size: 14px;
    }
    .modal-content form textarea {
      resize: vertical;
      min-height: 60px;
    }
    .modal-content form button {
      grid-column: 1 / -1;
      padding: 10px 0;
      background: #E53935;
      border: none;
      color: #FFF;
      font-size: 16px;
      border-radius: 4px;
      cursor: pointer;
      margin-top: 10px;
    }
    .modal-content form button:hover {
      background: #D32F2F;
    }

    /* --- Общие стили списка --- */
    .equipment-controls {
      margin-bottom: 15px;
      display: flex;
      gap: 10px;
    }
    .equipment-controls input {
      flex: 1;
      padding: 8px;
      border: 1px solid #CCC;
      border-radius: 4px;
      font-size: 14px;
    }
    .equipment-controls .red-button {
      padding: 8px 16px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    th, td {
      border: 1px solid #DDD;
      padding: 6px 10px;
      font-size: 13px;
      vertical-align: middle;
    }
    th {
      background: #F5F5F5;
    }
    img.thumb {
      width: 40px;
      height: auto;
      border-radius: 2px;
    }
    .notes ul {
      list-style: disc inside;
      font-size: 13px;
      color: #555;
    }
  </style>
</head>
<body>
<div class="wrapper">
  <header>
    <div class="header-content">
      <img src="../img/logo.png" class="logo" alt="Логотип">
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
  <h2 class="highlight">Список оборудования</h2>

    <div class="equipment-controls">
      <input type="text" id="search" placeholder="Поиск по названию…" oninput="filterEquipment()">
      <button class="red-button" onclick="openAddModal()">Добавить оборудование</button>
    </div>

    <div class="equipment-table">
      <table>
        <thead>
          <tr>
            <th>Фото</th><th>Название</th><th>Инв. номер</th><th>Аудитория</th>
            <th>Ответств.</th><th>Врем. отв.</th><th>Стоимость</th><th>Модель</th>
             <th>Тип оборудования</th>
            <th>Направл.</th><th>Статус</th><th>Комментарий</th><th>Действия</th>
          </tr>
        </thead>
        <tbody id="equipment-body"></tbody>
      </table>
    </div>

    <div class="notes">
      <ul>
        <li>При удалении оборудования, связанного с другими модулями, будет показано предупреждение.</li>
        <li>Инвентарный номер и название обязательны. Стоимость — только цифры и точка.</li>
      </ul>
    </div>
  </main>
  <footer>
    <!-- Ваш футер -->
  </footer>
</div>

<!-- Модальное окно -->
<div id="equipment-modal" class="modal">
  <div class="modal-content">
    <span class="close-button" onclick="closeModal()">&times;</span>
    <h3 id="modal-title">Добавить оборудование</h3>
    <form id="equipment-form" enctype="multipart/form-data">
      <input type="hidden" id="equipment-id" name="id">

      <label>Название*:
        <input type="text" name="name" id="name" required>
      </label>

      <label>Инвентарный номер*:
        <input type="number" id="inventory_number" name="inventory_number" min="1" max="999">
      </label>

      <label>Фото:
        <input type="file" name="photo" id="photo" accept="image/*">
      </label>

      <label>Аудитория:
        <select name="room_id" id="room_id"><option value="">—Выберите—</option></select>
      </label>

      <label>Ответств.:
        <select name="responsible_user_id" id="responsible_user_id"><option value="">—Выберите—</option></select>
      </label>

      <label>Врем. отв.:
        <select name="temporary_responsible_user_id" id="temporary_responsible_user_id"><option value="">—Выберите—</option></select>
      </label>

      <label>Стоимость:
        <input type="text" name="price" id="price">
      </label>

      <label>Модель:
        <select name="model_id" id="model_id"><option value="">—Выберите—</option></select>
      </label>

      <label class="full">Направление:
        <input type="text" name="direction_name" id="direction_name">
      </label>

      <label class="full">Статус:
        <input type="text" name="status" id="status">
      </label>
      <label class="full">Тип оборудования:
  <input type="text" name="equipment_type" id="equipment_type" placeholder="Например: Монитор, Принтер, ПК">
</label>

      <label class="full">Комментарий:
        <textarea name="comment" id="comment"></textarea>
      </label>

      <button type="submit">Сохранить</button>
    </form>
  </div>
</div>

<script>
// данные справочников и оборудования
let equipmentList = [], roomsList = [], usersList = [], modelsList = [], editingId = null;

document.addEventListener('DOMContentLoaded', ()=>{
  Promise.all([
    fetch('../controllers/RoomController.php?action=get').then(r=>r.json()),
    fetch('../controllers/UserController.php?action=get').then(r=>r.json()),
    fetch('../controllers/ModelController.php?action=get').then(r=>r.json())
  ]).then(([rooms, users, models])=>{
    roomsList  = rooms;
    usersList  = users.map(u=>({
      id: u.id,
      name: `${u.last_name} ${u.first_name}${u.middle_name? ' '+u.middle_name : ''}`
    }));
    modelsList = models;

    fillSelect('room_id', roomsList,  'name');
    fillSelect('responsible_user_id', usersList, 'name');
    fillSelect('temporary_responsible_user_id', usersList, 'name');
    fillSelect('model_id', modelsList, 'name');

    fetchEquipment();
  });
});

function fillSelect(id, arr, key) {
  const sel = document.getElementById(id);
  arr.forEach(o=>{
    const opt = document.createElement('option');
    opt.value = o.id;
    opt.textContent = o[key];
    sel.append(opt);
  });
}

function fetchEquipment() {
  fetch('../controllers/EquipmentController.php?action=get')
    .then(r=>r.json())
    .then(data=>{ equipmentList = data; renderEquipment(data); });
}

function renderEquipment(arr) {
  const body = document.getElementById('equipment-body');
  body.innerHTML = '';
  arr.forEach(e=>{
    body.insertAdjacentHTML('beforeend', `
      <tr>
        <td>${e.photo?`<img src="${e.photo}" class="thumb">`:''}</td>
        <td>${e.name}</td>
        <td>${e.inventory_number}</td>
        <td>${e.room_name||''}</td>
        <td>${e.responsible_user_name||''}</td>
        <td>${e.temporary_responsible_user_name||''}</td>
        <td>${e.price!==null?e.price:''}</td>
        <td>${e.model_name||''}</td>
        <td>${e.equipment_type || ''}</td>
        <td>${e.direction_name||''}</td>
        <td>${e.status||''}</td>
        <td>${e.comment||''}</td>
        <td>
          <button onclick="openEditModal(${e.id})">Изм.</button>
          <button onclick="deleteEquipment(${e.id})">Удал.</button>
        </td>
      </tr>
    `);
  });
}

function filterEquipment() {
  const q = document.getElementById('search').value.toLowerCase();
  renderEquipment(equipmentList.filter(e=>e.name.toLowerCase().includes(q)));
}

function openAddModal() {
  editingId = null;
  document.getElementById('modal-title').textContent = 'Добавить оборудование';
  document.getElementById('equipment-form').reset();
  document.getElementById('equipment-modal').style.display = 'flex';
}

function openEditModal(id) {
  editingId = id;
  const e = equipmentList.find(x => x.id === id);
  document.getElementById('modal-title').textContent = 'Редактировать оборудование';

  const fields = [
  'name', 'inventory_number', 'price', 'direction_name',
  'status', 'comment', 'room_id', 'responsible_user_id',
  'temporary_responsible_user_id', 'model_id', 'equipment_type'
];


  fields.forEach(key => {
    const el = document.getElementById(key);
    if (el) el.value = e[key] ?? '';
  });

  document.getElementById('equipment-modal').style.display = 'flex';
}


function closeModal(){
  document.getElementById('equipment-modal').style.display = 'none';
}
document.addEventListener('click', e=>{
  if(e.target.id==='equipment-modal') closeModal();
});

document.getElementById('equipment-form').addEventListener('submit', ev=>{
  ev.preventDefault();
  const form = ev.target;
  const name = form.name.value.trim();
  const inv  = form.inventory_number.value.trim();
  if(!name) return alert('Название обязательно');
  if(!inv)  return alert('Инвентарный номер обязателен');

  const price = form.price.value.trim();
  if(price && !/^\d+(\.\d+)?$/.test(price))
    return alert('Стоимость — только цифры или точка');

  const fd = new FormData(form);
  fd.set('action', editingId ? 'update' : 'create');
  if(editingId) fd.set('id', editingId);

  fetch('../controllers/EquipmentController.php', {
    method:'POST',
    body: fd
  })
  .then(r=>r.json())
  .then(res=>{
    if(res.status==='success') {
      closeModal();
      fetchEquipment();
    } else {
      alert('Ошибка: '+(res.message||'неизвестная'));
    }
  })
  .catch(err=>alert('Сетевая ошибка: '+err));
});

function deleteEquipment(id) {
  if(!confirm('Удалить оборудование?')) return;
  const fd = new FormData();
  fd.set('action','destroy');
  fd.set('id',id);
  fetch('../controllers/EquipmentController.php',{
    method:'POST',
    body:fd
  })
  .then(_=>fetchEquipment());
}
</script>
</body>
</html>