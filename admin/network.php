<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Сетевые настройки | Учёт оборудования</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <style>
    /* === Модалка === */
    .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:1000; }
    .modal-content { background:#fff; padding:20px; border-radius:8px; max-width:500px; width:90%; position:relative; box-shadow:0 4px 20px rgba(0,0,0,0.2); }
    .close-btn { position:absolute; top:10px; right:10px; font-size:24px; cursor:pointer; color:#666; }
    .close-btn:hover { color:#000; }
    .status { font-weight:bold; }

    /* === Контролы === */
    .equipment-controls { display:flex; flex-direction:column; gap:10px; margin-bottom:20px; width:100%; }
    .equipment-controls input { width:100%; padding:10px; font-size:16px; border:1px solid #CCC; border-radius:4px; box-sizing:border-box; }
    .equipment-controls .red-button { width:100%; padding:10px; box-sizing:border-box; }

    /* === Таблица === */
    table { width:100%; border-collapse:collapse; margin-bottom:20px; }
    th,td { border:1px solid #DDD; padding:10px; text-align:left; font-size:13px; }
    th { background:#F5F5F5; }
    tr:hover { background:#f9f9f9; }

    /* Сортировка */
    th.sortable { cursor:pointer; user-select:none; }
    th.sortable.asc::after  { content:" \2191"; }
    th.sortable.desc::after { content:" \2193"; }

    /* === Форма модалки === */
    .modal-content form { display:grid; grid-gap:15px; }
    .modal-content label { display:flex; flex-direction:column; font-size:14px; color:#333; }
    .modal-content input[type="text"],
    .modal-content select { margin-top:6px; padding:8px; border:1px solid #CCC; border-radius:4px; font-size:14px; width:100%; box-sizing:border-box; }
    .modal-content button[type="submit"] { padding:10px; background:#E53935; color:#FFF; border:none; border-radius:4px; font-size:16px; cursor:pointer; margin-top:10px; }
    .modal-content button[type="submit"]:hover { background:#D32F2F; }
    </style>
</head>
<body>
  <div class="wrapper">
    <header>
      <div class="header-content">
        <img src="../img/logo.png" alt="Логотип" class="logo">
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
      <h2 class="highlight">Настройки сети</h2>
      <div class="equipment-controls">
        <input id="search" type="text" placeholder="Поиск по IP или оборудованию" oninput="applySortFilter()">
        <button class="red-button" onclick="openCreateModal()">Добавить настройки</button>
        <button class="red-button" onclick="checkNetwork()">Проверить сети</button>
      </div>
      <table>
        <thead>
          <tr>
            <th class="sortable" data-field="id">ID</th>
            <th class="sortable" data-field="equipment_name">Оборудование</th>
            <th class="sortable" data-field="ip_address">IP-адрес</th>
            <th class="sortable" data-field="subnet_mask">Маска</th>
            <th class="sortable" data-field="gateway">Шлюз</th>
            <th class="sortable" data-field="dns_servers">DNS</th>
            <th class="sortable" data-field="status">Статус</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody id="network-body"></tbody>
      </table>
    </main>
  </div>

  <!-- === Модальное окно === -->
  <div id="ns-modal" class="modal">
    <div class="modal-content">
      <span class="close-btn" onclick="closeModal()">&times;</span>
      <h3 id="modal-title">Добавить настройки</h3>
      <form id="ns-form">
        <input type="hidden" id="ns-id">
        <label>IP-адрес:<input type="text" id="ip_address" required></label>
        <label>Маска подсети:<input type="text" id="subnet_mask" required></label>
        <label>Шлюз:<input type="text" id="gateway" required></label>
        <label>DNS (через запятую):<input type="text" id="dns_servers"></label>
        <label>Оборудование:
          <select id="equipment_id" required>
            <option value="">— выберите —</option>
          </select>
        </label>
        <button type="submit" class="red-button">Сохранить</button>
      </form>
    </div>
  </div>

<script>
const API = '../controllers/NetworkSettingsController.php';
let settings = [], equipment = [], editId = null;
let sortField = 'id';
let sortAsc   = true;

document.addEventListener('DOMContentLoaded', () => {
  fetchNetworkSettings();
  fetchEquipmentList();
  document.getElementById('ns-form').addEventListener('submit', onSave);
  window.addEventListener('click', e => e.target.id === 'ns-modal' && closeModal());

  // обработчики сортировки
  document.querySelectorAll('th.sortable').forEach(th => th.addEventListener('click', () => changeSort(th)));
});

async function fetchNetworkSettings() {
  const res = await fetch(`${API}?action=get`);
  settings = await res.json();
  applySortFilter();
}

async function fetchEquipmentList() {
  const res = await fetch(`${API}?action=equipment`);
  equipment = await res.json();
  const sel = document.getElementById('equipment_id');
  sel.innerHTML = '<option value="">— выберите —</option>';
  equipment.forEach(e => {
    const o = document.createElement('option');
    o.value = e.id; o.textContent = e.name;
    sel.append(o);
  });
}

function renderSettings(list) {
  document.getElementById('network-body').innerHTML = list.map(n => `
    <tr id="row-${n.id}">
      <td>${n.id}</td>
      <td>${n.equipment_name}</td>
      <td>${n.ip_address}</td>
      <td>${n.subnet_mask || ''}</td>
      <td>${n.gateway || ''}</td>
      <td>${n.dns_servers || ''}</td>
      <td class="status">${n.status || ''}</td>
      <td>
        <button onclick="openEditModal(${n.id})">Изм.</button>
        <button onclick="deleteSetting(${n.id})">Удал.</button>
      </td>
    </tr>`).join('');
}

function changeSort(th) {
  const field = th.dataset.field;
  if (sortField === field) {
    sortAsc = !sortAsc;
  } else {
    sortField = field;
    sortAsc   = true;
  }
  // визуальный индикатор
  document.querySelectorAll('th.sortable').forEach(h => h.classList.remove('asc', 'desc'));
  th.classList.add(sortAsc ? 'asc' : 'desc');
  applySortFilter();
}

function applySortFilter() {
  const q = document.getElementById('search').value.toLowerCase();
  // фильтр
  let list = q ? settings.filter(n => n.ip_address.includes(q) || n.equipment_name.toLowerCase().includes(q)) : [...settings];
  // сортировка
  const dir = sortAsc ? 1 : -1;
  list.sort((a, b) => {
    let av = a[sortField] ?? '';
    let bv = b[sortField] ?? '';
    // Особая логика для status (online/offline) чтобы online был "выше"
    if (sortField === 'status') {
      const val = v => v === 'online' ? 1 : v === 'offline' ? 0 : -1;
      return dir * (val(av) - val(bv));
    }
    // числовая сортировка для id
    if (sortField === 'id') return dir * (av - bv);
    return dir * av.toString().localeCompare(bv.toString(), 'ru', { sensitivity:'base' });
  });
  renderSettings(list);
}

function openCreateModal() {
  editId = null;
  document.getElementById('modal-title').textContent = 'Добавить настройки';
  document.getElementById('ns-form').reset();
  document.getElementById('ns-id').value = '';
  document.getElementById('ns-modal').style.display = 'flex';
}

function openEditModal(id) {
  const n = settings.find(x => x.id === id);
  if (!n) return;
  editId = id;
  document.getElementById('modal-title').textContent = 'Изменить настройки';
  document.getElementById('ns-id').value = id;
  document.getElementById('ip_address').value = n.ip_address;
  document.getElementById('subnet_mask').value = n.subnet_mask || '';
  document.getElementById('gateway').value = n.gateway || '';
  document.getElementById('dns_servers').value = n.dns_servers || '';
  document.getElementById('equipment_id').value = n.equipment_id;
  document.getElementById('ns-modal').style.display = 'flex';
}

function closeModal() { document.getElementById('ns-modal').style.display = 'none'; }

async function onSave(e) {
  e.preventDefault();
  const data = {
    ip_address:  document.getElementById('ip_address').value.trim(),
    subnet_mask: document.getElementById('subnet_mask').value.trim(),
    gateway:     document.getElementById('gateway').value.trim(),
    dns_servers: document.getElementById('dns_servers').value.trim(),
    equipment_id:+document.getElementById('equipment_id').value
  };
  const payload = editId ? { action:'update', id:editId, data } : { action:'create', data };
  await fetch(API, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload) });
  closeModal();
  fetchNetworkSettings();
}

async function deleteSetting(id) {
  if (!confirm('Удалить эту запись?')) return;
  await fetch(API, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({ action:'delete', id }) });
  fetchNetworkSettings();
}

async function checkNetwork() {
  try {
    const res = await fetch(`${API}?action=check`);
    const stats = await res.json();
    stats.forEach(s => {
      const cell = document.querySelector(`#row-${s.id} .status`);
      if (cell) cell.textContent = s.status;
      // обновляем локальный кэш
      const rec = settings.find(x => x.id === s.id);
      if (rec) rec.status = s.status;
    });
    applySortFilter();
  } catch (err) { console.error('Ошибка при проверке сети:', err); }
}
</script>
</body>
</html>
