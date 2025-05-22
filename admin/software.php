<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Программы | Учёт оборудования</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <style>
    /* Стили таблицы и модалки (копировать из equipment.php, rooms.php) */
    .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); align-items:center; justify-content:center; z-index:1000; }
    .modal-content { background:#fff; border-radius:8px; padding:20px; width:90%; max-width:500px; box-shadow:0 4px 20px rgba(0,0,0,0.2); position:relative; }
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
    th,td { border:1px solid #DDD; padding:6px 10px; font-size:13px; }
    th { background:#F5F5F5; }

    .modal-content form {
      display:grid;
      grid-template-columns:1fr 1fr;
      grid-gap:15px 20px;
    }
    .modal-content label {
      display:flex; flex-direction:column; font-size:14px;
    }
    .modal-content label.full { grid-column:1/-1; }
    .modal-content input[type="text"] {
      margin-top:6px; padding:8px; border:1px solid #CCC; border-radius:4px;
    }
    .modal-content button {
      grid-column:1/-1; padding:10px; background:#E53935; color:#fff; border:none; border-radius:4px; cursor:pointer;
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
    <h2 class="highlight">Список программ</h2>

<div class="controls">
  <input type="text" id="search" placeholder="Поиск по названию…" oninput="filterSoft()">
  <button class="red-button" onclick="openAdd()">Добавить программу</button>
</div>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Название</th>
      <th>Версия</th>
      <th>Разработчик</th>
      <th>Действия</th>
    </tr>
  </thead>
  <tbody id="soft-body"></tbody>
</table>
</main>
<footer><!-- ваш footer --></footer>
</div>

<!-- Модальное окно -->
<div id="soft-modal" class="modal">
<div class="modal-content">
<span class="close-button" onclick="closeModal()">&times;</span>
<h3 id="modal-title">Добавить программу</h3>
<form id="soft-form">
  <input type="hidden" id="soft-id" name="id">

  <label>Название*:
    <input type="text" id="soft-name" name="name" required>
  </label>

  <label>Версия:
    <input type="text" id="soft-version" name="version">
  </label>

  <label class="full">Разработчик:
    <input type="text" id="soft-dev" name="developer_name">
  </label>

  <button type="submit">Сохранить</button>
</form>
</div>
</div>

<script>
let list = [], editingId = null;

// Загрузка списка
function fetchSoft() {
fetch('../controllers/SoftwareController.php?action=get')
.then(r=>r.json())
.then(arr=>{ list = arr; renderSoft(arr); });
}

// Отрисовка
function renderSoft(arr) {
const b = document.getElementById('soft-body');
b.innerHTML = '';
arr.forEach(s=>{
b.insertAdjacentHTML('beforeend', `
  <tr>
    <td>${s.id}</td>
    <td>${s.name}</td>
    <td>${s.version||''}</td>
    <td>${s.developer_name||''}</td>
    <td>
      <button onclick="openEdit(${s.id})">Изм.</button>
      <button onclick="del(${s.id})">Удал.</button>
    </td>
  </tr>
`);
});
}

// Поиск
function filterSoft() {
const q = document.getElementById('search').value.toLowerCase();
renderSoft(list.filter(s=>s.name.toLowerCase().includes(q)));
}

// Модалка
function openAdd() {
editingId = null;
document.getElementById('modal-title').textContent = 'Добавить программу';
document.getElementById('soft-form').reset();
document.getElementById('soft-modal').style.display = 'flex';
}
function openEdit(id) {
editingId = id;
const s = list.find(x=>x.id===id);
document.getElementById('modal-title').textContent = 'Редактировать программу';
document.getElementById('soft-id').value      = s.id;
document.getElementById('soft-name').value    = s.name;
document.getElementById('soft-version').value = s.version||'';
document.getElementById('soft-dev').value     = s.developer_name||'';
document.getElementById('soft-modal').style.display = 'flex';
}
function closeModal() {
document.getElementById('soft-modal').style.display = 'none';
}
document.addEventListener('click', e=>{
if (e.target.id==='soft-modal') closeModal();
});

// Сохранение (create/update)
document.getElementById('soft-form').addEventListener('submit', ev=>{
ev.preventDefault();
const name = document.getElementById('soft-name').value.trim();
if (!name) return alert('Название обязательно');

const fd = new FormData(ev.target);
fd.set('action', editingId ? 'update' : 'create');
if (editingId) fd.set('id', editingId);

fetch('../controllers/SoftwareController.php', {
method:'POST', body: fd
})
.then(r=>r.json())
.then(res=>{
if(res.status==='success'){
  closeModal(); fetchSoft();
} else {
  alert('Ошибка: '+(res.message||'неизвестная'));
}
})
.catch(err=>alert('Сетевая ошибка: '+err));
});

// Удаление
function del(id) {
if (!confirm('Удалить программу?')) return;
const fd = new FormData();
fd.set('action','destroy');
fd.set('id',id);
fetch('../controllers/SoftwareController.php', {
method:'POST', body:fd
})
.then(r=>r.json())
.then(res=>{
if (res.status==='success') fetchSoft();
else alert('Ошибка: '+(res.message||'неизвестная'));
});
}

document.addEventListener('DOMContentLoaded', fetchSoft);
</script>
</body>
</html>