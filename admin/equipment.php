<?php 

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оборудование | Админ</title>
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
        <h2 class="highlight">Список оборудования</h2>

        <div class="equipment-controls">
            <input type="text" placeholder="Поиск по названию...">
            <button class="red-button">Добавить оборудование</button>
        </div>

        <div class="equipment-table">
            <table>
                <thead>
                    <tr>
                        <th>Фото</th>
                        <th>Название</th>
                        <th>Инв. номер</th>
                        <th>Аудитория</th>
                        <th>Ответственный</th>
                        <th>Вр. ответственный</th>
                        <th>Стоимость</th>
                        <th>Направление</th>
                        <th>Статус</th>
                        <th>Модель</th>
                        <th>Комментарий</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody id="equipment-body">
            <!-- сюда подставит JS -->
          </tbody>
            </table>
        </div>

        <p style="margin-top: 30px; font-size: 14px; color: #555;">
            * При удалении оборудования, связанного с другими модулями, будет показано предупреждение.<br>
            * Инвентарный номер и название обязательны. Проверка: только цифры в номере, только цифры в стоимости.
        </p>
    </main>

    <footer>
        &copy; 2025 Учебное заведение. Все права защищены.
    </footer>

</div>
<script>
    const API = '../controllers/EquipmentController.php?action=get';

async function fetchEquipment() {
  try {
    const res  = await fetch(API);
    const list = await res.json();
    const tbody = document.getElementById('equipment-body');
    tbody.innerHTML = '';

    list.forEach(item => {
      tbody.innerHTML += `
        <tr>
          <td>${ item.photo 
              ? `<img src="data:image/jpeg;base64,${btoa(item.photo)}" style="height:40px">`
              : `<img src="../img/no-photo.png" style="height:40px">`
          }</td>
          <td>${item.name}</td>
          <td>${item.inventory_number}</td>
          <td>${item.room_id  || ''}</td>
          <td>${item.responsible_user_id  || ''}</td>
          <td>${item.temporary_responsible_user_id  || ''}</td>
          <td>${item.price  ? item.price + ' ₽' : ''}</td>
          <td>${item.direction_name || ''}</td>
          <td>${item.status || ''}</td>
          <td>${item.model_id || ''}</td>
          <td>${item.comment || ''}</td>
          <td class="table-actions">
            <a href="#" onclick="alert('Edit пока не готов')">Редактировать</a>
            <a href="#" onclick="alert('Delete пока не готов')">Удалить</a>
          </td>
        </tr>`;
    });
  } catch (err) {
    console.error('Error loading equipment:', err);
  }
}

document.addEventListener('DOMContentLoaded', fetchEquipment);
    function toggleMenu() {
        const nav = document.getElementById('mobileMenu');
        nav.classList.toggle('open');
    }
</script>
</body>
</html>
