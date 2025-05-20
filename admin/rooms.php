<?php

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Аудитории | Админ</title>
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
        <h2 class="highlight">Учёт аудиторий</h2>

        <div class="equipment-controls">
            <input type="text" placeholder="Поиск по наименованию...">
            <button class="red-button">Добавить аудиторию</button>
        </div>

        <div class="equipment-table">
            <table>
                <thead>
                    <tr>
                        <th>Полное наименование</th>
                        <th>Сокращение</th>
                        <th>Ответственный</th>
                        <th>Временно ответственный</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody id="rooms-body">
          <!-- JS сюда подставит все аудитории -->
        </tbody>
            </table>
        </div>

        <p style="margin-top: 30px; font-size: 14px; color: #555;">
            * Поле "наименование" обязательно для заполнения.<br>
            * При удалении аудитории, связанной с другим оборудованием — система уведомит пользователя.
        </p>
    </main>

    <footer>
        &copy; 2025 Учебное заведение. Все права защищены.
    </footer>

</div>
<script>
    const API = '../controllers/RoomController.php?action=get';

async function fetchRooms() {
  try {
    const res  = await fetch(API);
    const list = await res.json();
    const tbody = document.getElementById('rooms-body');
    tbody.innerHTML = '';

    list.forEach(r => {
      tbody.innerHTML += `
        <tr>
          <td>${r.name}</td>
          <td>${r.short_name || ''}</td>
          <td>${r.responsible_name || '-'}</td>
          <td>${r.temporary_responsible_name || '-'}</td>
          <td class="table-actions">
            <a href="#" onclick="alert('Редактирование пока не реализовано')">Редактировать</a>
            <a href="#" onclick="alert('Удаление пока не реализовано')">Удалить</a>
          </td>
        </tr>`;
    });
  } catch (err) {
    console.error('Error loading rooms list:', err);
  }
}

document.addEventListener('DOMContentLoaded', fetchRooms);

document.addEventListener('DOMContentLoaded', fetchRooms);
    function toggleMenu() {
        const nav = document.getElementById('mobileMenu');
        nav.classList.toggle('open');
    }
</script>
</body>
</html>
