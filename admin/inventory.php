<?php 

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Инвентаризация | Учёт оборудования</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="wrapper">

    <header>
    <div class="header-content">
        <img src="../img/logo.png" alt="Логотип" class="logo">
        <h1>Система учёта оборудования</h1>
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
        <h2 class="highlight">Инвентаризация оборудования</h2>

        <p>В этом разделе администратор может запускать новые инвентаризации, указывать дату, название, а также отслеживать результаты проверок, кто из пользователей проверял оборудование, с комментариями и статусами.</p>

        <div class="equipment-controls">
            <input type="text" placeholder="Поиск по названию инвентаризации">
            <button class="red-button">Добавить инвентаризацию</button>
        </div>

        <h3>Список инвентаризаций</h3>
        <div class="equipment-table">
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
        </div>

        <div class="equipment-table">
        <h2 id="results-heading" style="margin-top:20px;">Результаты инвентаризации</h2>
            <table>
                <thead>
                    <tr>
                        <th>Оборудование</th>
                        <th>Пользователь</th>
                        <th>Комментарий</th>
                        <th>Статус</th>
                    </tr>
                </thead>
                <tbody id="results-body"></tbody>
            </table>
        </div>

    </main>

    <footer>
        &copy; 2025 Учебное заведение. Все права защищены.
    </footer>

</div>
<script>
    const API = '../controllers/InventoryCheckController.php';

document.addEventListener('DOMContentLoaded', () => {
  fetchChecks();
  document.getElementById('checks-body').addEventListener('click', e => {
    if (e.target.matches('.show-results')) {
      fetchResults(e.target.dataset.id);
    }
  });
});

async function fetchChecks() {
  const res    = await fetch(`${API}?action=getChecks`);
  const checks = await res.json();
  const tb     = document.getElementById('checks-body');
  tb.innerHTML = '';
  checks.forEach(c => {
    tb.innerHTML += `
      <tr>
        <td>${c.id}</td>
        <td>${c.name}</td>
        <td>${c.start_date}</td>
        <td>${c.end_date}</td>
        <td><button class="show-results" data-id="${c.id}">Оборудование</button></td>
      </tr>`;
  });
  if (checks.length) fetchResults(checks[0].id);
}

async function fetchResults(checkId) {
  document.getElementById('results-heading').textContent =
    `Результаты инвентаризации №${checkId}`;

  const res     = await fetch(`${API}?action=getResults&id=${checkId}`);
  const data    = await res.json();
  const tbody   = document.getElementById('results-body');

  if (!Array.isArray(data)) {
    console.error('Expected array, got', data);
    return;
  }

  tbody.innerHTML = data.map(r => `
    <tr>
      <td>${r.equipment_name} (ID:${r.equipment_id})</td>
      <td>${r.checked_by    || '-'}</td>
      <td>${r.comment       || ''}</td>
      <td>${r.status        || ''}</td>
    </tr>
  `).join('');
}

document.addEventListener('DOMContentLoaded', fetchChecks);
    function toggleMenu() {
        const nav = document.getElementById('mobileMenu');
        nav.classList.toggle('open');
    }
</script>
</body>
</html>
