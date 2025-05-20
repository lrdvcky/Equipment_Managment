<?php 

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Сетевые настройки | Учёт оборудования</title>
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
        <h2 class="highlight">Настройки сети</h2>

        <div class="equipment-controls">
            <input type="text" placeholder="Поиск по IP или оборудованию">
            <button class="red-button">Добавить настройки</button>
        </div>

        <div class="equipment-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Оборудование</th>
                        <th>IP-адрес</th>
                        <th>Маска подсети</th>
                        <th>Шлюз</th>
                        <th>DNS-серверы</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody id="network-body">
    <!-- сюда JS подставит строки -->
  </tbody>
            </table>
        </div>
    </main>

    <footer>
        &copy; 2025 Учебное заведение. Все права защищены.
    </footer>

</div>
<script>
    const API = '../controllers/NetworkSettingsController.php';

document.addEventListener('DOMContentLoaded', fetchNetworkSettings);

async function fetchNetworkSettings() {
  try {
    const res  = await fetch(`${API}?action=get`);
    const list = await res.json();
    const tbody = document.getElementById('network-body');

    if (!Array.isArray(list)) {
      console.error('Ожидался массив сетевых настроек:', list);
      return;
    }

    tbody.innerHTML = list.map(n => `
  <tr>
    <td>${n.id}</td>
    <!-- раньше: <td>${n.equipment_id}</td> -->
    <td>${n.equipment_name}</td>
    <td>${n.ip_address}</td>
    <td>${n.subnet_mask}</td>
    <td>${n.gateway}</td>
    <td>${n.dns_servers}</td>
    <td class="table-actions">
      <a href="#" onclick="alert('Изменение пока не готово');return false;">Изм.</a>
      <a href="#" onclick="alert('Удаление пока не готово');return false;">Удал.</a>
    </td>
  </tr>
`).join('');

  } catch (err) {
    console.error('Error loading network settings:', err);
  }
}
    function toggleMenu() {
        const nav = document.getElementById('mobileMenu');
        nav.classList.toggle('open');
    }
</script>
</body>
</html>
