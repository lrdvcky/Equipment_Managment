<?php 

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Расходные материалы | Админ</title>
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
        <h2 class="highlight">Учёт расходных материалов</h2>

        <div class="equipment-controls">
            <input type="text" placeholder="Поиск по наименованию...">
            <button class="red-button">Добавить расходник</button>
        </div>

        <div class="equipment-table">
            <table>
                <thead>
                    <tr>
                        <th>Фото</th>
                        <th>Наименование</th>
                        <th>Описание</th>
                        <th>Дата поступления</th>
                        <th>Количество</th>
                        <th>Тип</th>
                        <th>Ответственный</th>
                        <th>Временно ответственный</th>
                        <th>Характеристики</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody id="consumables-body">
            <!-- JS подставит строки -->
          </tbody>
            </table>
        </div>

        <p style="margin-top: 30px; font-size: 14px; color: #555;">
            * Проверка: дата поступления в формате ДД.ММ.ГГГГ, количество — только цифры.<br>
            * Связанные с оборудованием расходники отображаются в карточке оборудования.<br>
            * Удаление связанных расходников и характеристик сопровождается уведомлением.
        </p>
    </main>

    <footer>
        &copy; 2025 Учебное заведение. Все права защищены.
    </footer>

</div>
<script>
    const API = '../controllers/ConsumableController.php?action=get';

document.addEventListener('DOMContentLoaded', fetchConsumables);

async function fetchConsumables() {
  const res  = await fetch(API);
  const list = await res.json();
  console.log('RAW consumables:', list);
  if (!Array.isArray(list)) return;
  const tbody = document.getElementById('consumables-body');
  tbody.innerHTML = list.map(c => `
    <tr>
      <td>${c.id}</td>
      <td>${c.name}</td>
      <td>${c.description || ''}</td>
      <td>${c.arrival_date || ''}</td>
      <td>${c.quantity}</td>
      <td>${c.type_name}</td>
      <td>${c.responsible_name}</td>
      <td>${c.temporary_responsible_name}</td>
      <td>${c.properties}</td>
      <td>
        <button>Изм.</button>
        <button>Удал.</button>
      </td>
    </tr>
  `).join('');
}
    function toggleMenu() {
        const nav = document.getElementById('mobileMenu');
        nav.classList.toggle('open');
    }
</script>
</body>
</html>
