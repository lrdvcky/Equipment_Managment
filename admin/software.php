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
    /* копируем стили из примера */
    .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); align-items:center; justify-content:center; z-index:1000; }
    .modal-content { background:#fff; border-radius:8px; padding:20px; width:90%; max-width:500px; box-shadow:0 4px 20px rgba(0,0,0,0.2); position:relative; }
    .close-button { position:absolute; top:10px; right:10px; font-size:24px; cursor:pointer; color:#666; }
    .close-button:hover { color:#000; }

    .controls { display:flex; gap:10px; margin-bottom:20px; }
    .controls input { flex:1; padding:8px; border:1px solid #CCC; border-radius:4px; }
    .controls .red-button { padding:8px 16px; }

    table { width:100%; border-collapse:collapse; margin-bottom:20px; }
    th, td { border:1px solid #DDD; padding:6px 10px; font-size:13px; }
    th { background:#F5F5F5; cursor:pointer; }
    tr.selected { background:#eef; }

    .modal-content form { display:grid; grid-template-columns:1fr 1fr; grid-gap:15px 20px; }
    .modal-content label { display:flex; flex-direction:column; font-size:14px; }
    .modal-content label.full { grid-column:1/-1; }
    .modal-content input[type="text"], .modal-content select { margin-top:6px; padding:8px; border:1px solid #CCC; border-radius:4px; }
    .modal-content form button { grid-column:1/-1; padding:10px; background:#E53935; color:#fff; border:none; border-radius:4px; cursor:pointer; }
    .modal-content form button:hover { background:#D32F2F; }
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
    <h2 class="highlight">Список программ</h2>

    <div class="controls">
      <input type="text" id="search" placeholder="Поиск…" oninput="filterSoft()">
      <button class="red-button" onclick="openAdd()">Добавить программу</button>
    </div>

    <table>
      <thead>
        <tr>
          <th data-field="id"># ▲▼</th>
          <th data-field="name">Название ▲▼</th>
          <th data-field="version">Версия ▲▼</th>
          <th data-field="developer_name">Разработчик ▲▼</th>
          <th data-field="equipment">Оборудование ▲▼</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody id="soft-body"></tbody>
    </table>
  </main>
  <footer>…</footer>
</div>

<!-- Модалка -->
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

      <label class="full">Оборудование:
        <select id="equipment_ids" name="equipment_ids[]" multiple size="5">
          <!-- options через JS -->
        </select>
      </label>

      <button type="submit">Сохранить</button>
    </form>
  </div>
</div>

<script>
let list = [], editingId = null;
let equipmentList = [];
let sortField = null, sortAsc = true;

document.addEventListener('DOMContentLoaded', ()=>{
  fetchEquipmentList();   // для select
  fetchSoftList();        // основная таблица

  // сортировка по клику
  document.querySelectorAll('th[data-field]').forEach(th=>{
    th.onclick = ()=>sortBy(th.dataset.field);
  });

  // сабмит формы
  document.getElementById('soft-form').addEventListener('submit', saveSoft);
  // закрытие по клику вне
  window.addEventListener('click', e=>{
    if (e.target.id==='soft-modal') closeModal();
  });
});

function fetchEquipmentList(){
  fetch('../controllers/EquipmentController.php?action=get')
    .then(r=>r.json())
    .then(arr=>{
      equipmentList = arr;
      const sel = document.getElementById('equipment_ids');
      sel.innerHTML = '';
      arr.forEach(e=>{
        sel.insertAdjacentHTML('beforeend',
          `<option value="${e.id}">${e.name}</option>`);
      });
    });
}

function fetchSoftList(){
  fetch('../controllers/SoftwareController.php?action=get')
    .then(r=>r.json())
    .then(arr=>{ 
      // подготовим поле equipment как массив
      list = arr.map(s=>({
        ...s,
        equipment: s.equipment // уже массив имён
      }));
      renderSoft(list);
    });
}

function renderSoft(arr){
  const b = document.getElementById('soft-body');
  b.innerHTML = '';
  arr.forEach(s=>{
    const eq = (s.equipment||[]).join(', ');
    b.insertAdjacentHTML('beforeend', `
      <tr ${s.id===editingId?'class="selected"':''}>
        <td>${s.id}</td>
        <td>${s.name}</td>
        <td>${s.version||''}</td>
        <td>${s.developer_name||''}</td>
        <td>${eq}</td>
        <td>
          <button onclick="event.stopPropagation(); openEdit(${s.id})">Изм.</button>
          <button onclick="event.stopPropagation(); del(${s.id})">Удал.</button>
        </td>
      </tr>
    `);
  });
}

function filterSoft(){
  const q = document.getElementById('search').value.toLowerCase();
  renderSoft(list.filter(s=>
    s.name.toLowerCase().includes(q) ||
    (s.developer_name||'').toLowerCase().includes(q) ||
    (s.equipment||[]).join(', ').toLowerCase().includes(q)
  ));
}

function sortBy(field){
  if (sortField===field) sortAsc = !sortAsc; else { sortField=field; sortAsc=true; }
  list.sort((a,b)=>{
    let va = a[field], vb = b[field];
    // если equipment — массив, делаем строку
    if (Array.isArray(va)) va = va.join(', ');
    if (Array.isArray(vb)) vb = vb.join(', ');
    va = (va||'').toString().toLowerCase();
    vb = (vb||'').toString().toLowerCase();
    if (va < vb) return sortAsc?-1:1;
    if (va > vb) return sortAsc?1:-1;
    return 0;
  });
  renderSoft(list);
}

function openAdd(){
  editingId = null;
  document.getElementById('modal-title').textContent = 'Добавить программу';
  document.getElementById('soft-form').reset();
  closeSelection();
  openModal();
}
function openEdit(id){
  editingId = id;
  const s = list.find(x=>x.id===id);
  document.getElementById('modal-title').textContent = 'Редактировать программу';
  document.getElementById('soft-id').value      = s.id;
  document.getElementById('soft-name').value    = s.name;
  document.getElementById('soft-version').value = s.version||'';
  document.getElementById('soft-dev').value     = s.developer_name||'';

  // выставляем selected в multi-select
  closeSelection();
  (s.equipment||[]).forEach(name=>{
    const opt = Array.from(document.getElementById('equipment_ids').options)
      .find(o=>o.text===name);
    if(opt) opt.selected = true;
  });

  openModal();
}
function closeSelection(){
  Array.from(document.getElementById('equipment_ids').options)
       .forEach(o=>o.selected=false);
}

function openModal(){ document.getElementById('soft-modal').style.display='flex'; }
function closeModal(){ document.getElementById('soft-modal').style.display='none'; }

function saveSoft(ev){
  ev.preventDefault();
  const f = document.getElementById('soft-form');
  if (!f.checkValidity()) { f.reportValidity(); return; }

  const fd = new FormData(f);
  fd.set('action', editingId ? 'update' : 'create');
  if (editingId) fd.set('id', editingId);

  // FormData соберёт все выбранные equipment_ids[] автоматически

  fetch('../controllers/SoftwareController.php',{
    method:'POST', body: fd
  })
  .then(r=>r.json())
  .then(res=>{
    if(res.status==='success'){
      closeModal();
      fetchSoftList();
    } else {
      alert('Ошибка: '+(res.message||'неизвестная'));
    }
  })
  .catch(err=>alert('Сетевая ошибка: '+err));
}

function del(id){
  if(!confirm('Удалить программу?')) return;
  const fd = new FormData();
  fd.set('action','destroy');
  fd.set('id',id);
  fetch('../controllers/SoftwareController.php',{
    method:'POST', body: fd
  })
  .then(r=>r.json())
  .then(res=>{
    if(res.status==='success') fetchSoftList();
    else alert('Ошибка: '+(res.message||'неизвестная'));
  });
}
</script>
</body>
</html>
