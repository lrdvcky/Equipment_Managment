<?php 

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Программы | Учёт оборудования</title>
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
        <h2 class="highlight">Управление программным обеспечением</h2>

        <div class="equipment-controls">
            <input type="text" placeholder="Поиск по названию или разработчику">
            <button class="red-button">Добавить программу</button>
        </div>

        <div class="equipment-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Версия</th>
                        <th>Разработчик</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody id="software-body">
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
    async function fetchSoftware() {
  try {
    const res = await fetch('../controllers/SoftwareController.php?action=get');
    const text = await res.text();                  // <- читаем как текст
    console.log('RAW RESPONSE:', text);             // <- смотрим в консоли
    const list = JSON.parse(text);  

        const tbody = document.getElementById('software-body');
        tbody.innerHTML = '';

        list.forEach(s => {
          tbody.innerHTML += `
            <tr>
              <td>${s.id}</td>
              <td>${s.name}</td>
              <td>${s.version ?? ''}</td>
              <td>${s.developer_name ?? ''}</td>
            </tr>`;
        });
      } catch (err) {
    console.error('Error loading software list:', err);
  }
    }

    document.addEventListener('DOMContentLoaded', fetchSoftware);
    function toggleMenu() {
        const nav = document.getElementById('mobileMenu');
        nav.classList.toggle('open');
    }
</script>
</body>
</html>
