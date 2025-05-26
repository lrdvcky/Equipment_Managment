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
      font-size: 14px; color: #333;
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
      resize: vertical; min-height: 60px;
    }
    .modal-content form button {
      grid-column: 1 / -1;
      padding: 10px 0;
      background: #E53935;
      border: none; color: #FFF;
      font-size: 16px; border-radius: 4px;
      cursor: pointer; margin-top: 10px;
    }
    .modal-content form button:hover {
      background: #D32F2F;
    }

    /* --- Общие стили списка --- */
    .equipment-controls {
      margin-bottom: 15px; display: flex; gap: 10px;
    }
    .equipment-controls input {
      flex: 1; padding: 8px;
      border: 1px solid #CCC; border-radius: 4px;
      font-size: 14px;
    }
    .equipment-controls .red-button {
      padding: 8px 16px;
    }
    table {
      width: 100%; border-collapse: collapse; margin-bottom: 10px;
    }
    th, td {
      border: 1px solid #DDD; padding: 6px 10px;
      font-size: 13px; vertical-align: middle;
    }
    th { background: #F5F5F5; cursor: pointer; }
    tr.selected { background: #eef; }
    img.thumb {
      width: 40px; height: auto; border-radius: 2px;
    }
    #history-section {
      margin-top: 20px;
      padding: 15px;
      border: 1px solid #DDD;
      background: #FAFAFA;
    }
    #history-section h3 {
      margin-top: 0;
    }
    #history-list li {
      font-size: 13px; color: #333;
      margin-bottom: 4px;
    }
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
    <h2 class="highlight">Список оборудования</h2>

    <div class="equipment-controls">
      <input type="text" id="search" placeholder="Поиск по названию…" oninput="filterEquipment()">
      <button class="red-button" onclick="openAddModal()">Добавить оборудование</button>
    </div>

    <div class="equipment-table">
      <table>
        <thead>
          <tr>
            <th data-field="photo">Фото</th>
            <th data-field="name">Название</th>
            <th data-field="inventory_number">Инв. номер</th>
            <th data-field="room_name">Аудитория</th>
            <th data-field="responsible_user_name">Ответств.</th>
            <th data-field="temporary_responsible_user_name">Врем. отв.</th>
            <th data-field="price">Стоимость</th>
            <th data-field="model_name">Модель</th>
            <th data-field="equipment_type">Тип оборудования</th>
            <th data-field="direction_name">Направл.</th>
            <th data-field="status">Статус</th>
            <th data-field="comment">Комментарий</th>
            <th data-field="inventory_section">Раздел</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody id="equipment-body"></tbody>
      </table>
    </div>

    <!-- Секция истории -->
    <section id="history-section">
      <h3>История смен ответственных</h3>
      <p id="history-note">Кликните по строке оборудования, чтобы увидеть историю.</p>
      <ul id="history-list">
        <!-- список li записей -->
      </ul>
    </section>
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
        <input type="text" id="inventory_number" name="inventory_number" required>
      </label>

      <label>Фото:
        <input type="file" name="photo" id="photo" accept="image/*">
      </label>

      <label>Аудитория*:
        <select name="room_id" id="room_id" required>
          <option value="">—Выберите—</option>
        </select>
      </label>

      <label>Ответств.*:
        <select name="responsible_user_id" id="responsible_user_id" required>
          <option value="">—Выберите—</option>
        </select>
      </label>

      <label>Врем. отв.:
        <select name="temporary_responsible_user_id" id="temporary_responsible_user_id">
          <option value="">—Выберите—</option>
        </select>
      </label>

      <label>Стоимость:
        <input type="text" name="price" id="price" placeholder="123.45">
      </label>

      <label>Модель*:
        <input type="text" name="model_name" id="model_name" required placeholder="Введите модель">
      </label>

      <label class="full">Тип оборудования*:
        <input type="text" name="equipment_type" id="equipment_type" required placeholder="Монитор, Принтер…">
      </label>

      <label class="full">Направление:
        <input type="text" name="direction_name" id="direction_name">
      </label>

      <label class="full">Статус:
        <input type="text" name="status" id="status">
      </label>

      <label class="full">Комментарий:
        <textarea name="comment" id="comment"></textarea>
      </label>

      <label class="full">Раздел инвентаризации*:
        <select name="inventory_section" id="inventory_section" required>
          <option value="">—Выберите раздел—</option>
        </select>
      </label>

      <button type="submit">Сохранить</button>
    </form>
  </div>
</div>


<script>
  let equipmentList = [], roomsList = [], usersList = [], checksList = [];
  let editingId = null, sortField = null, sortAsc = true;

  document.addEventListener('DOMContentLoaded', ()=>{

    // 1) Загрузить разделы (инвентаризации)
    fetch('../controllers/InventoryCheckController.php?action=getChecks')
      .then(r=>r.json()).then(data=>{
        checksList = data;
        const sel = document.getElementById('inventory_section');
        data.forEach(c=>{
          sel.insertAdjacentHTML('beforeend',
            `<option value="${c.name}">${c.name}</option>`);
        });
      });

    // 2) Загрузить справочники
    Promise.all([
      fetch('../controllers/RoomController.php?action=get').then(r=>r.json()),
      fetch('../controllers/UserController.php?action=get').then(r=>r.json())
    ]).then(([rooms, users])=>{
      roomsList = rooms;
      usersList = users.map(u=>({
        id: u.id,
        name: `${u.last_name} ${u.first_name}${u.middle_name? ' '+u.middle_name : ''}`
      }));
      fillSelect('room_id', roomsList, 'name');
      fillSelect('responsible_user_id', usersList, 'name');
      fillSelect('temporary_responsible_user_id', usersList, 'name');
      fetchEquipment();
    });

    // 3) Обработчик формы
    document.getElementById('equipment-form')
      .addEventListener('submit', saveEquipment);
  });

  function fillSelect(id, arr, key) {
    const sel = document.getElementById(id);
    arr.forEach(o=>{
      sel.insertAdjacentHTML('beforeend',
        `<option value="${o.id}">${o[key]}</option>`);
    });
  }

  // Загрузка списка
  function fetchEquipment() {
    fetch('../controllers/EquipmentController.php?action=get')
      .then(r=>r.json())
      .then(data=>{ equipmentList = data; renderEquipment(data); });
  }

  // Отрисовка таблицы
  function renderEquipment(arr) {
    const body = document.getElementById('equipment-body');
    body.innerHTML = '';
    arr.forEach(e=>{
      const rowClass = (e.id===editingId? 'selected':'' );
      body.insertAdjacentHTML('beforeend', `
        <tr class="${rowClass}" onclick="loadHistory(${e.id}, this)">
          <td>${e.photo? `<img src="${e.photo}" class="thumb">` : ''}</td>
          <td>${e.name}</td>
          <td>${e.inventory_number}</td>
          <td>${e.room_name||''}</td>
          <td>${e.responsible_user_name||''}</td>
          <td>${e.temporary_responsible_user_name||''}</td>
          <td>${e.price||''}</td>
          <td>${e.model_name||''}</td>
          <td>${e.equipment_type||''}</td>
          <td>${e.direction_name||''}</td>
          <td>${e.status||''}</td>
          <td>${e.comment||''}</td>
          <td>${e.inventory_section||''}</td>
          <td>
            <button onclick="event.stopPropagation(); openEditModal(${e.id})">Изм.</button>
            <button onclick="event.stopPropagation(); deleteEquipment(${e.id})">Удал.</button>
          </td>
        </tr>
      `);
    });
    // повесим сортировку на шапку
    document.querySelectorAll('th[data-field]').forEach(th=>{
      th.onclick = ()=>sortBy(th.dataset.field);
    });
  }

  // Поиск
  function filterEquipment() {
    const q = document.getElementById('search').value.toLowerCase();
    renderEquipment(
      equipmentList.filter(e=>e.name.toLowerCase().includes(q))
    );
  }

  // Сортировка
  function sortBy(field) {
    if (sortField===field) sortAsc = !sortAsc;
    else { sortField = field; sortAsc = true; }
    equipmentList.sort((a,b)=>{
      if (a[field] < b[field]) return sortAsc? -1:1;
      if (a[field] > b[field]) return sortAsc? 1:-1;
      return 0;
    });
    renderEquipment(equipmentList);
  }

  // Подгрузка истории для выбранного оборудования
  function loadHistory(equipId, row) {
    editingId = equipId;
    document.querySelectorAll('tr').forEach(r=>r.classList.remove('selected'));
    row.classList.add('selected');
    fetch(`../controllers/EquipmentController.php?action=getHistory&equip=${equipId}`)
      .then(r=>r.json())
      .then(lst=>{
        const ul = document.getElementById('history-list');
        document.getElementById('history-note').style.display = 'none';
        ul.innerHTML = '';
        if (!lst.length) {
          ul.innerHTML = '<li>Нет записей.</li>';
        } else {
          lst.forEach(h=>{
            ul.insertAdjacentHTML('beforeend',
              `<li>${h.changed_at} — ${h.user_name}: ${h.comment}</li>`);
          });
        }
      });
  }

  // Открыть «Добавить»
  function openAddModal() {
    editingId = null;
    document.getElementById('modal-title').textContent = 'Добавить оборудование';
    document.getElementById('equipment-form').reset();
    document.getElementById('equipment-modal').style.display = 'flex';
  }

  // Открыть «Редактировать»
  function openEditModal(id) {
    editingId = id;
    const e = equipmentList.find(x=>x.id===id);
    document.getElementById('modal-title').textContent = 'Редактировать оборудование';
    ['name','inventory_number','price','direction_name','status','comment']
      .forEach(k=> document.getElementById(k).value = e[k]||'');
    ['room_id','responsible_user_id','temporary_responsible_user_id']
      .forEach(id=> document.getElementById(id).value = e[id]||'');
    document.getElementById('model_name').value = e.model_name||'';
    document.getElementById('equipment_type').value = e.equipment_type||'';
    document.getElementById('inventory_section').value = e.inventory_section||'';
    document.getElementById('equipment-modal').style.display = 'flex';
  }

  function closeModal(){
    document.getElementById('equipment-modal').style.display = 'none';
  }
  document.addEventListener('click', e=>{
    if(e.target.id==='equipment-modal') closeModal();
  });

  // Сохранение
  function saveEquipment(ev) {
    ev.preventDefault();
    const f = ev.target;
    if (!f.name.value.trim() || !f.inventory_number.value.trim()
        || !f.room_id.value || !f.responsible_user_id.value
        || !f.model_name.value.trim() || !f.equipment_type.value
        || !f.inventory_section.value) {
      return alert('Заполните все обязательные поля');
    }
    if (f.price.value && !/^\d+(\.\d+)?$/.test(f.price.value))
      return alert('Стоимость — цифры и точка.');

    const fd = new FormData(f);
    fd.set('action', editingId? 'update':'create');
    if (editingId) fd.set('id', editingId);

    fetch('../controllers/EquipmentController.php', { method:'POST', body:fd })
  .then(response => {
    if (!response.ok) {
      return response.text().then(text => { throw new Error(text) });
    }
    return response.json();
  })
    .then(res=>{
      if (res.status==='success') {
        closeModal(); fetchEquipment();
      } else alert('Ошибка: '+(res.message||'неизвестная'));
    })
    .catch(err => alert('Ошибка при обновлении:\n' + err.message));
  }

  // Удаление
  function deleteEquipment(id) {
    if(!confirm('Удалить оборудование?')) return;
    const fd = new FormData();
    fd.set('action','destroy');
    fd.set('id',id);
    fetch('../controllers/EquipmentController.php',{
      method:'POST', body:fd
    }).then(_=>fetchEquipment());
  }
</script>
</body>
</html>
