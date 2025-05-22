<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Аудитории | Админ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <style>
    /* копируем стили из equipment.php, адаптируя ширину формы */
    .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); align-items:center; justify-content:center; z-index:1000; }
    .modal-content { background:#fff; border-radius:8px; padding:20px; width:90%; max-width:550px; box-shadow:0 4px 20px rgba(0,0,0,0.2); position:relative; }
    .close-button { position:absolute; top:10px; right:10px; font-size:24px; cursor:pointer; color:#666; }
    .close-button:hover { color:#000; }

    .controls {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 20px;
    width: 100%;
}

.controls input {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid var(--gray);
    border-radius: 4px;
    box-sizing: border-box;
}

.controls .red-button {
    width: 100%;
    padding: 10px;
    box-sizing: border-box;
}
    table { width:100%; border-collapse:collapse; margin-bottom:20px; }
    th,td { border:1px solid #DDD; padding:6px 10px; vertical-align:middle; font-size:13px; }
    th { background:#F5F5F5; }

    /* форма в модалке */
    .modal-content form {
      display:grid;
      grid-template-columns:1fr 1fr;
      grid-gap:15px 20px;
    }
    .modal-content label {
      display:flex; flex-direction:column; font-size:14px; color:#333;
    }
    .modal-content label.full { grid-column:1/-1; }
    .modal-content input[type="text"],
    .modal-content select {
      margin-top:6px; padding:8px; border:1px solid #CCC; border-radius:4px; font-size:14px;
    }
    .modal-content button {
      grid-column:1/-1; padding:10px; background:#E53935; color:#fff; border:none; border-radius:4px; font-size:16px; cursor:pointer; margin-top:10px;
    }
    .modal-content button:hover { background:#D32F2F; }
  </style>
</head>
<body>
<div class="wrapper">

    <header>
    <div class="header-content">
        <img src="../img/logo.png" alt="Логотип" class="logo">
        <h1>Система учёта оборудования</h1>
        <a href="../logout.php" class="red-button" style="margin-bottom: 10px; text-decoration: none;">Выход</a>
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
    <h2 class="highlight">Список аудиторий</h2>

<div class="controls">
  <input type="text" id="search" placeholder="Поиск по названию…" oninput="filterRooms()">
  <button class="red-button" onclick="openAddModal()">Добавить аудиторию</button>
</div>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Название</th>
      <th>Краткое</th>
      <th>Ответств.</th>
      <th>Врем. отв.</th>
      <th>Действия</th>
    </tr>
  </thead>
  <tbody id="rooms-body"></tbody>
</table>
</main>
<footer><!-- ваш футер --></footer>
</div>

<!-- Модалка -->
<div id="rooms-modal" class="modal">
<div class="modal-content">
<span class="close-button" onclick="closeModal()">&times;</span>
<h3 id="modal-title">Добавить аудиторию</h3>
<form id="rooms-form">
  <input type="hidden" id="room-id" name="id">

  <label>Название*:
    <input type="text" id="room-name" name="name" required>
  </label>

  <label>Короткое имя:
    <input type="text" id="room-short" name="short_name">
  </label>

  <label>Ответственный:
    <select id="room-resp" name="responsible_user_id">
      <option value="">—Выберите—</option>
    </select>
  </label>

  <label>Врем. отв.:
    <select id="room-temp" name="temporary_responsible_user_id">
      <option value="">—Выберите—</option>
    </select>
  </label>

  <label class="full"> </label>
  <button type="submit">Сохранить</button>
</form>
</div>
</div>


<script>
let rooms = [], users = [], editingId = null;

document.addEventListener('DOMContentLoaded', ()=>{
  fetch('../controllers/UserController.php?action=get').then(r=>r.json()).then(u=>{
    users = u.map(x=>({id:x.id, name:`${x.last_name} ${x.first_name}${x.middle_name?' '+x.middle_name:''}`}));
    fillSelect('room-resp', users);
    fillSelect('room-temp', users);
    fetchRooms();
  });
});

function fillSelect(id, arr) {
  const sel = document.getElementById(id);
  arr.forEach(o=>{
    const opt = document.createElement('option');
    opt.value = o.id;
    opt.textContent = o.name;
    sel.append(opt);
  });
}

function fetchRooms() {
  fetch('../controllers/RoomController.php?action=get')
    .then(r=>r.json())
    .then(arr=>{ rooms=arr; renderRooms(arr); });
}

function renderRooms(arr) {
  const body = document.getElementById('rooms-body');
  body.innerHTML = '';
  arr.forEach(r=>{
    const resp = users.find(u=>u.id===r.responsible_user_id)?.name||'';
    const temp = users.find(u=>u.id===r.temporary_responsible_user_id)?.name||'';
    body.insertAdjacentHTML('beforeend', `
      <tr>
        <td>${r.id}</td>
        <td>${r.name}</td>
        <td>${r.short_name||''}</td>
        <td>${resp}</td>
        <td>${temp}</td>
        <td>
          <button onclick="openEdit(${r.id})">Изм.</button>
          <button onclick="del(${r.id})">Удал.</button>
        </td>
      </tr>`);
  });
}

function filterRooms() {
  const q = document.getElementById('search').value.toLowerCase();
  renderRooms(rooms.filter(r=>r.name.toLowerCase().includes(q)));
}

function openAddModal(){
  editingId = null;
  document.getElementById('modal-title').textContent='Добавить аудиторию';
  document.getElementById('rooms-form').reset();
  document.getElementById('rooms-modal').style.display='flex';
}

function openEdit(id){
  editingId = id;
  const r = rooms.find(x=>x.id===id);
  document.getElementById('modal-title').textContent='Редактировать аудиторию';
  document.getElementById('room-id').value = r.id;
  document.getElementById('room-name').value = r.name;
  document.getElementById('room-short').value= r.short_name||'';
  document.getElementById('room-resp').value = r.responsible_user_id||'';
  document.getElementById('room-temp').value = r.temporary_responsible_user_id||'';
  document.getElementById('rooms-modal').style.display='flex';
}

function closeModal(){
  document.getElementById('rooms-modal').style.display='none';
}
document.addEventListener('click', e=>{ if(e.target.id==='rooms-modal') closeModal(); });

// Тут исправленный блок обработки submit:
document.getElementById('rooms-form').addEventListener('submit', ev=>{
  ev.preventDefault();
  const roomName = document.getElementById('room-name').value.trim();
  if (!roomName) return alert('Название обязательно');

  const fd = new FormData();
  fd.set('action', editingId ? 'update' : 'create');
  if (editingId) fd.set('id', editingId);
  fd.set('name', roomName);
  fd.set('short_name', document.getElementById('room-short').value);
  fd.set('responsible_user_id', document.getElementById('room-resp').value);
  fd.set('temporary_responsible_user_id', document.getElementById('room-temp').value);

  fetch('../controllers/RoomController.php', {
    method: 'POST',
    body: fd
  })
  .then(r=>r.json())
  .then(res=>{
    if(res.status==='success'){
      closeModal(); fetchRooms();
    } else {
      alert('Ошибка: '+(res.message||'неизвестная'));
    }
  })
  .catch(err=>alert('Сетевая ошибка: '+err));
});

function del(id){
  if(!confirm('Удалить аудиторию?')) return;
  const fd = new FormData();
  fd.set('action','destroy');
  fd.set('id',id);
  fetch('../controllers/RoomController.php',{
    method:'POST', body:fd
  })
  .then(r=>r.json())
  .then(res=>{
    if(res.status==='success') fetchRooms();
    else alert('Ошибка: '+(res.message||'неизвестная'));
  });
}
</script>
</body>
</html>