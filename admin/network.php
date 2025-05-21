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
    /* Модалка */
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
      max-width: 400px;
      width: 90%;
      position: relative;
    }
    .close-btn {
      position: absolute;
      top: 10px; right: 10px;
      font-size: 20px;
      cursor: pointer;
    }
    .modal-content label {
      display: block;
      margin-bottom: 10px;
    }
    .modal-content input {
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
        <h2 class="highlight">Настройки сети</h2>
      <div class="equipment-controls">
        <input id="search" type="text" placeholder="Поиск по IP или оборудованию" oninput="filterSettings()">
        <button class="red-button" onclick="openCreateModal()">Добавить настройки</button>
      </div>
      <div class="equipment-table">
        <table>
          <thead>
            <tr>
              <th>ID</th><th>Оборудование</th><th>IP-адрес</th><th>Маска</th><th>Шлюз</th><th>DNS</th><th>Действия</th>
            </tr>
          </thead>
          <tbody id="network-body"></tbody>
        </table>
      </div>
    </main>
  </div>

  <!-- Модальное окно -->
  <div id="ns-modal" class="modal">
    <div class="modal-content">
      <span class="close-btn" onclick="closeModal()">&times;</span>
      <h3 id="modal-title">Добавить настройки</h3>
      <form id="ns-form">
        <input type="hidden" id="ns-id">
        <label>IP-адрес:<input type="text" id="ip_address" required></label>
        <label>Маска подсети:<input type="text" id="subnet_mask"></label>
        <label>Шлюз:<input type="text" id="gateway"></label>
        <label>DNS:<input type="text" id="dns_servers"></label>
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

    // загрузка и рендеринг
    document.addEventListener('DOMContentLoaded', () => {
      fetchNetworkSettings();
      fetchEquipmentList();
      document.getElementById('ns-form').addEventListener('submit', onSave);
      window.addEventListener('click', e => e.target.id==='ns-modal' && closeModal());
    });

    async function fetchNetworkSettings() {
      const res = await fetch(`${API}?action=get`);
      settings = await res.json();
      renderSettings(settings);
    }

    async function fetchEquipmentList() {
      const res = await fetch(`${API}?action=equipment`);
      equipment = await res.json();
      const sel = document.getElementById('equipment_id');
      equipment.forEach(e => {
        const o = document.createElement('option');
        o.value = e.id; o.textContent = e.name;
        sel.append(o);
      });
    }

    function renderSettings(list) {
      document.getElementById('network-body').innerHTML = list.map(n=>`
        <tr>
          <td>${n.id}</td>
          <td>${n.equipment_name}</td>
          <td>${n.ip_address}</td>
          <td>${n.subnet_mask||''}</td>
          <td>${n.gateway||''}</td>
          <td>${n.dns_servers||''}</td>
          <td>
            <button onclick="openEditModal(${n.id})">Изм.</button>
            <button onclick="deleteSetting(${n.id})">Удал.</button>
          </td>
        </tr>
      `).join('');
    }

    function filterSettings() {
      const q = document.getElementById('search').value.toLowerCase();
      renderSettings(settings.filter(n =>
        n.ip_address.includes(q) ||
        n.equipment_name.toLowerCase().includes(q)
      ));
    }

    function openCreateModal() {
      editId = null;
      document.getElementById('modal-title').textContent = 'Добавить настройки';
      document.getElementById('ns-form').reset();
      closeModal(); // сбросить предыдущие listeners
      document.getElementById('ns-id').value = '';
      document.getElementById('ns-modal').style.display = 'flex';
    }

    function openEditModal(id) {
      editId = id;
      const n = settings.find(x => x.id===id);
      document.getElementById('modal-title').textContent = 'Редактировать настройки';
      document.getElementById('ns-id').value         = n.id;
      document.getElementById('ip_address').value    = n.ip_address;
      document.getElementById('subnet_mask').value   = n.subnet_mask||'';
      document.getElementById('gateway').value       = n.gateway||'';
      document.getElementById('dns_servers').value   = n.dns_servers||'';
      document.getElementById('equipment_id').value  = n.equipment_id||'';
      document.getElementById('ns-modal').style.display = 'flex';
    }

    function closeModal() {
      document.getElementById('ns-modal').style.display = 'none';
    }

    async function deleteSetting(id) {
      if (!confirm('Удалить эту запись?')) return;
      await fetch(API, {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({action:'delete',id})
      });
      fetchNetworkSettings();
    }

    async function onSave(e) {
      e.preventDefault();
      const data = {
        ip_address:   document.getElementById('ip_address').value,
        subnet_mask:  document.getElementById('subnet_mask').value||null,
        gateway:      document.getElementById('gateway').value||null,
        dns_servers:  document.getElementById('dns_servers').value||null,
        equipment_id: parseInt(document.getElementById('equipment_id').value,10)
      };
      const payload = editId
        ? { action:'update', id:editId, data }
        : { action:'create', data };
      console.log('👉 API URL:', API);
      console.log('👉 Payload:', payload);
      try {
    const resp = await fetch(API, {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify(payload)
    });
    const text = await resp.text();
    console.log('🛑 Raw response (text):', text);
    // если это валидный JSON, дальше его распарсим
    try {
      console.log('✅ Parsed JSON:', JSON.parse(text));
    } catch {
      console.warn('⚠️ Response is not JSON');
    }
  } catch(err) {
    console.error('❌ Fetch failed:', err);
  }
      await fetch(API, {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify(payload)
      });

      closeModal();
      fetchNetworkSettings();
    }
  </script>
</body>
</html>