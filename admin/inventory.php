<?php
// inventory.php
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Инвентаризация | Учёт оборудования</title>
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <link rel="stylesheet" href="../style.css">
  <style>
    .modal { display:none; position:fixed; top:0; left:0;
             width:100%; height:100%; background:rgba(0,0,0,0.6);
             align-items:center; justify-content:center; z-index:1000; }
    .modal-content { background:#FFF; border-radius:8px; padding:20px;
                     width:90%; max-width:600px; box-shadow:0 4px 20px rgba(0,0,0,0.2);
                     position:relative; }
    .close { position:absolute; top:10px; right:10px; font-size:24px;
             cursor:pointer; color:#666; }
    .close:hover { color:#000; }

    .modal-content form { display:grid; grid-template-columns:1fr; grid-gap:15px; }
    .modal-content form label { display:flex; flex-direction:column;
                                 font-size:14px; color:#333; }
    .modal-content form input[type="text"],
    .modal-content form input[type="date"] { margin-top:6px; padding:8px;
      border:1px solid #CCC; border-radius:4px; font-size:14px;
      width:100%; box-sizing:border-box; }
    .modal-content form button { padding:10px 0; background:#E53935;
      border:none; color:#FFF; font-size:16px; border-radius:4px;
      cursor:pointer; margin-top:10px; }
    .modal-content form button:hover { background:#D32F2F; }

    .controls { display:flex; flex-direction:column; gap:10px;
                margin-bottom:20px; width:100%; }
    .controls input { width:100%; padding:10px; font-size:16px;
                      border:1px solid #CCC; border-radius:4px;
                      box-sizing:border-box; }
    .controls .red-button { width:100%; padding:10px; box-sizing:border-box; }

    table { width:100%; border-collapse:collapse; margin-bottom:20px; }
    th, td { border:1px solid #DDD; padding:6px 10px;
              vertical-align:middle; font-size:13px; }
    th { background:#F5F5F5; }
  </style>
</head>
<body>
<div class="wrapper">
  <header>
    <div class="header-content">
      <img src="../img/logo.png" class="logo" alt="Логотип">
      <h1>Система учёта оборудования</h1>
      <a href="../logout.php" class="red-button"
         style="margin-bottom:10px;text-decoration:none;">Выход</a>
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
    <div class="controls">
      <input type="text" id="search-checks" placeholder="Поиск по наименованию…">
      <button id="add-check-btn" class="red-button">Добавить инвентаризацию</button>
    </div>

    <h3>Список инвентаризаций</h3>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Наименование</th>
          <th>Дата начала</th>
          <th>Дата окончания</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody id="checks-body"></tbody>
    </table>

    <div id="results-section" style="display:none;">
      <h3>Результаты инвентаризации:
        <span id="results-check-name"></span>
      </h3>
      <table>
        <thead>
          <tr>
            <th>Наименование оборудования</th>
            <th>Проверено пользователем</th>
            <th>Комментарий</th>
            <th>Статус</th>
          </tr>
        </thead>
        <tbody id="results-body"></tbody>
      </table>
    </div>
  </main>
</div>

<!-- Модалка для CRUD -->
<div id="check-modal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3 id="modal-title">Новая инвентаризация</h3>
    <form id="check-form">
      <input type="hidden" id="check-id" name="id">
      <label>Наименование<br>
        <input type="text" id="check-name" name="name" required>
      </label>
      <label>Дата начала<br>
        <input type="date" id="check-start" name="start_date">
      </label>
      <label>Дата окончания<br>
        <input type="date" id="check-end" name="end_date">
      </label>
      <button type="submit">Сохранить</button>
    </form>
  </div>
</div>

<script>
  const API   = '../controllers/InventoryCheckController.php';
  const modal = document.getElementById('check-modal');
  const form  = document.getElementById('check-form');

  document.addEventListener('DOMContentLoaded', () => {
    loadChecks();

    // Фильтрация списка
    document.getElementById('search-checks').addEventListener('input', e => {
      const f = e.target.value.trim().toLowerCase();
      document.querySelectorAll('#checks-body tr').forEach(r => {
        r.style.display = r.children[1].textContent.toLowerCase().includes(f)
                         ? '' : 'none';
      });
    });

    // Открыть форму добавления
    document.getElementById('add-check-btn').addEventListener('click', () => {
      form.reset();
      document.getElementById('modal-title').textContent = 'Новая инвентаризация';
      document.getElementById('check-id').value = '';
      modal.style.display = 'flex';
    });

    // Закрытие формы
    document.querySelector('#check-modal .close')
      .addEventListener('click', () => modal.style.display = 'none');
    window.addEventListener('click', e => {
      if (e.target === modal) modal.style.display = 'none';
    });

    // Обработка кнопок «Результаты», «Изм.», «Удал.»
    document.getElementById('checks-body').addEventListener('click', async e => {
      const btn = e.target;
      const id  = btn.dataset.id;
      const row = btn.closest('tr');

      if (btn.matches('.show-results')) {
        loadResults(id, row.children[1].textContent.trim());
      }
      else if (btn.matches('.edit-check')) {
        document.getElementById('modal-title').textContent = 'Редактировать инвентаризацию';
        document.getElementById('check-id').value    = id;
        document.getElementById('check-name').value  = row.children[1].textContent.trim();
        document.getElementById('check-start').value = row.children[2].textContent.trim();
        document.getElementById('check-end').value   = row.children[3].textContent.trim();
        modal.style.display = 'flex';
      }
      else if (btn.matches('.delete-check')) {
        if (!confirm('Удалить инвентаризацию?')) return;
        await fetch(`${API}?action=deleteCheck&id=${id}`);
        document.getElementById('results-section').style.display = 'none';
        loadChecks();
      }
    });

    // Сохранение формы (add/update)
    form.addEventListener('submit', async ev => {
      ev.preventDefault();
      const id    = document.getElementById('check-id').value;
      const name  = document.getElementById('check-name').value.trim();
      if (!name) return alert('Наименование обязательно');
      const start = document.getElementById('check-start').value || '';
      const end   = document.getElementById('check-end').value   || '';
      const action= id ? 'updateCheck' : 'addCheck';
      const params= new URLSearchParams({ action, name, start_date: start, end_date: end });
      if (id) params.set('id', id);

      try {
        const res  = await fetch(`${API}?${params}`);
        const json = await res.json();
        if (json.status === 'ok') {
          modal.style.display = 'none';
          loadChecks();
        } else {
          alert('Ошибка: ' + (json.message||'неизвестная'));
        }
      } catch (err) {
        alert('Сетевая ошибка: ' + err);
      }
    });
  });

  // Загрузка списка инвентаризаций
  async function loadChecks() {
    const res  = await fetch(`${API}?action=getChecks`);
    const data = await res.json();
    document.getElementById('checks-body').innerHTML = data.map(c => `
      <tr>
        <td>${c.id}</td>
        <td>${c.name}</td>
        <td>${c.start_date||''}</td>
        <td>${c.end_date  ||''}</td>
        <td>
          <button class="show-results" data-id="${c.id}">Результаты</button>
          <button class="edit-check"    data-id="${c.id}">Изм.</button>
          <button class="delete-check"  data-id="${c.id}">Удал.</button>
        </td>
      </tr>
    `).join('');
  }

  // Показ результатов выбранной инвентаризации
  async function loadResults(checkId, checkName) {
    const res  = await fetch(`${API}?action=getResults&id=${checkId}`);
    const body = await res.json();
    if (!res.ok) {
      alert('Ошибка: ' + (body.message||res.statusText));
      return;
    }
    if (!Array.isArray(body)) {
      console.error('Ожидался массив, получили:', body);
      return;
    }
    document.getElementById('results-check-name').textContent = checkName;
    document.getElementById('results-body').innerHTML = body.map(r => `
      <tr>
        <td>${r.equipment_name}</td>
        <td>${r.user_fullname||'—'}</td>
        <td>${r.comment}</td>
        <td>${r.checked == 1 ? '✔️' : '✖️'}</td>
      </tr>
    `).join('');
    document.getElementById('results-section').style.display = 'block';
  }
</script>
</body>
</html>
