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
    table { width:100%; border-collapse:collapse; margin-bottom:20px; }
    th, td { border:1px solid #DDD; padding:6px 10px; font-size:13px; vertical-align:middle; }
    th { background:#F5F5F5; cursor:pointer; }
    img.thumb { width:40px; height:auto; border-radius:2px; }
    .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); align-items:center; justify-content:center; z-index:1000; }
    .modal-content { background:#FFF; border-radius:8px; padding:20px; width:90%; max-width:600px; position:relative; }
    .close-button { position:absolute; top:10px; right:10px; font-size:24px; cursor:pointer; color:#666; }
    .close-button:hover { color:#000; }
    .modal-content form { display:grid; grid-gap:10px; }
    .modal-content form label { display:flex; flex-direction:column; font-size:14px; color:#333; }
    .modal-content form input,
    .modal-content form select,
    .modal-content form textarea { margin-top:4px; padding:8px; border:1px solid #CCC; border-radius:4px; font-size:14px; }
    .modal-content form button { padding:10px; background:#E53935; border:none; color:#FFF; border-radius:4px; cursor:pointer; font-size:15px; }
    .modal-content form button:hover { background:#D32F2F; }
    #history-section { margin-top:20px; padding:15px; border:1px solid #DDD; background:#FAFAFA; }
    #history-list li { font-size:13px; margin-bottom:4px; }
    tr.selected { background:#eef; }
  </style>
</head>
<body>
<div class="wrapper">

  <header>
    <div class="header-content">
      <img src="../img/logo.png" class="logo" alt="Логотип">
      <h1>Система учёта оборудования</h1>
      <a href="../logout.php" class="red-button">Выход</a>
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
      <input id="search-input" type="text" placeholder="Поиск по наименованию…" oninput="filterList()">
      <button id="add-btn" class="red-button" onclick="openAddModal()">Добавить расходник</button>
    </div>

    <table>
      <thead>
        <tr>
          <th data-field="photo">Фото</th>
          <th data-field="name">Наименование</th>
          <th data-field="description">Описание</th>
          <th data-field="arrival_date">Дата поступления</th>
          <th data-field="quantity">Количество</th>
          <th data-field="type_name">Тип</th>
          <th data-field="responsible_name">Ответственный</th>
          <th data-field="temporary_responsible_name">Временно отв.</th>
          <th data-field="properties">Характеристики</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody id="consumables-body"></tbody>
    </table>

    <section id="history-section">
      <h3>История смен ответственных</h3>
      <p id="history-note">Кликните по строке, чтобы увидеть историю.</p>
      <ul id="history-list"></ul>
    </section>
  </main>

  <footer>&copy; 2025 Учебное заведение. Все права защищены.</footer>
</div>

<!-- Модалка добавления/редактирования -->
<div id="consumable-modal" class="modal">
  <div class="modal-content">
    <span class="close-button" onclick="closeModal()">&times;</span>
    <h3 id="modal-title">Добавить расходник</h3>
    <form id="consumable-form" enctype="multipart/form-data">
      <input type="hidden" id="consumable-id" name="id">

      <label>Фото:
        <input type="file" id="photo" name="photo" accept="image/*">
      </label>

      <label>Наименование*:
        <input type="text" id="name" name="name" required>
      </label>

      <label>Описание:
        <textarea id="description" name="description"></textarea>
      </label>

      <label>Дата поступления*:
        <input type="text" id="arrival_date" name="arrival_date" placeholder="ДД.MM.ГГГГ" required>
      </label>

      <label>Количество*:
        <input type="text" id="quantity" name="quantity" required>
      </label>

      <label>Тип*:
        <select id="consumable_type_id" name="consumable_type_id" required>
          <option value="">— Выберите —</option>
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

      <label>Комментарий к истории:
        <input type="text" id="history_comment" name="history_comment" placeholder="Причина смены">
      </label>

      <button type="submit">Сохранить</button>
    </form>
  </div>
</div>

<script>
  const API = '../controllers/ConsumableController.php';
  let consumables = [], editingId = null, sortField = null, sortAsc = true;

  document.addEventListener('DOMContentLoaded', () => {
    loadTypes(); loadUsers(); fetchConsumables();
    document.getElementById('add-btn').onclick = openAddModal;
    document.getElementById('search-input').oninput = filterList;
    document.getElementById('consumable-form').onsubmit = saveConsumable;
    document.getElementById('consumables-body').onclick = onTableClick;
  });

  async function loadTypes() {
    const types = await fetch(`${API}?action=getTypes`).then(r=>r.json());
    types.forEach(t => {
      document.getElementById('consumable_type_id')
        .insertAdjacentHTML('beforeend',
          `<option value="${t.id}">${t.name}</option>`);
    });
  }

  async function loadUsers() {
    const users = await fetch(`${API}?action=getUsers`).then(r=>r.json());
    users.forEach(u => {
      const opt = `<option value="${u.id}">${u.name}</option>`;
      document.getElementById('responsible_user_id').insertAdjacentHTML('beforeend',opt);
      document.getElementById('temporary_responsible_user_id').insertAdjacentHTML('beforeend',opt);
    });
  }

  async function fetchConsumables() {
    consumables = await fetch(`${API}?action=get`).then(r=>r.json());
    renderList(consumables);
  }

  function renderList(arr) {
    const body = document.getElementById('consumables-body');
    body.innerHTML = arr.map(c => `
      <tr data-id="${c.id}" onclick="loadHistory(${c.id}, this)">
        <td>${c.photo?`<img src="${c.photo}" class="thumb">`:''}</td>
        <td>${c.name}</td>
        <td>${c.description||''}</td>
        <td>${c.arrival_date}</td>
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
    document.querySelectorAll('th[data-field]').forEach(th=>{
      th.onclick = ()=>sortBy(th.dataset.field);
    });
  }

  function filterList() {
    const q = this.value.toLowerCase();
    renderList(consumables.filter(c=>c.name.toLowerCase().includes(q)));
  }

  function sortBy(field) {
    if (sortField===field) sortAsc = !sortAsc;
    else { sortField=field; sortAsc=true; }
    consumables.sort((a,b)=>{
      let va=a[field]||'', vb=b[field]||'';
      if (typeof va==='string') va=va.toLowerCase(), vb=vb.toLowerCase();
      return va<vb ? (sortAsc?-1:1) : (va>vb ? (sortAsc?1:-1) : 0);
    });
    renderList(consumables);
  }

  function openAddModal() {
    editingId=null;
    document.getElementById('modal-title').textContent='Добавить расходник';
    document.getElementById('consumable-form').reset();
    document.getElementById('consumable-modal').style.display='flex';
  }
  function openEditModal(id) {
    editingId=id;
    const c = consumables.find(x=>x.id==id);
    document.getElementById('modal-title').textContent='Редактировать расходник';
    ['name','description','arrival_date','quantity','history_comment']
      .forEach(f=>document.getElementById(f).value=c[f]||'');
    ['consumable_type_id','responsible_user_id','temporary_responsible_user_id']
      .forEach(f=>document.getElementById(f).value=c[f]||'');
    document.getElementById('consumable-id').value=c.id;
    document.getElementById('consumable-modal').style.display='flex';
  }
  function closeModal() {
    document.getElementById('consumable-modal').style.display='none';
  }
  window.onclick=e=>{ if(e.target.id==='consumable-modal') closeModal(); };

  async function saveConsumable(ev) {
    ev.preventDefault();
    const f = ev.target;
    if (!f.checkValidity()) { f.reportValidity(); return; }
    const fd = new FormData(f);
    fd.set('action', editingId?'update':'create');
    if (editingId) fd.set('id', editingId);
    const res = await fetch(API,{method:'POST',body:fd});
    const js  = await res.json();
    if (js.status==='success') { closeModal(); fetchConsumables(); }
    else alert('Ошибка: '+(js.message||res.statusText));
  }

  async function deleteConsumable(id) {
  if (!confirm('Удалить расходник?')) return;

  const fd = new FormData();
  fd.append('action', 'delete');   //  ←  слово как в контроллере
  fd.append('id', id);

  const res = await fetch(API, {
    method: 'POST',
    body:   fd,
    credentials: 'same-origin'
  });
  const j = await res.json();
  if (j.status === 'success') fetchConsumables();
  else alert('Не удалось удалить: ' + (j.message || res.statusText));
}


  async function loadHistory(id,row) {
    document.querySelectorAll('tr').forEach(r=>r.classList.remove('selected'));
    row.classList.add('selected');
    const lst = await fetch(`${API}?action=getHistory&consumable=${id}`)
                      .then(r=>r.json());
    const ul = document.getElementById('history-list');
    document.getElementById('history-note').style.display='none';
    ul.innerHTML = lst.length
      ? lst.map(h=>`<li>${h.changed_at} — ${h.user_name}: ${h.comment}</li>`).join('')
      : '<li>Нет записей.</li>';
  }

  function onTableClick(e) {
    if (e.target.classList.contains('edit-btn')) {
      e.stopPropagation(); openEditModal(e.target.dataset.id);
    }
    if (e.target.classList.contains('delete-btn')) {
      e.stopPropagation(); deleteConsumable(e.target.dataset.id);
    }
  }

  function toggleMenu() {
    document.getElementById('mobileMenu').classList.toggle('open');
  }
</script>
</body>
</html>
