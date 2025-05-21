<?php 
// inventory.php
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Инвентаризация | Учёт оборудования</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <style>
      /* Стили модального окна (точно как в equipment.php) */
      .modal {
        display: none; position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); align-items: center; justify-content: center;
      }
      .modal-content {
        background: #FFF; border-radius: 8px;
        padding: 20px; max-width: 500px; width: 90%;
        position: relative;
      }
      .modal-content .close {
        position: absolute; top: 10px; right: 10px;
        font-size: 24px; cursor: pointer;
      }
      .modal-content form { display: grid; gap: 10px; }
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
    <button id="add-check-btn" class="red-button">Добавить инвентаризацию</button>

    <h3>Список инвентаризаций</h3>
    <div class="equipment-table">
      <table>
        <thead>
          <tr>
            <th>ID</th><th>Наименование</th><th>Дата начала</th><th>Дата окончания</th><th>Действия</th>
          </tr>
        </thead>
        <tbody id="checks-body"></tbody>
      </table>
    </div>
  </main>

  <!-- Модальное окно -->
  <div id="check-modal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h3 id="modal-title">Новая инвентаризация</h3>
      <form id="check-form">
        <input type="hidden" name="id" id="check-id" />
        <label>Наименование
          <input type="text" name="name" id="check-name" required />
        </label>
        <label>Дата начала
          <input type="date" name="start_date" id="check-start" />
        </label>
        <label>Дата окончания
          <input type="date" name="end_date" id="check-end" />
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

      // Открыть форму на добавление
      document.getElementById('add-check-btn').addEventListener('click', () => {
        form.reset();
        document.getElementById('modal-title').textContent = 'Новая инвентаризация';
        document.getElementById('check-id').value = '';
        modal.style.display = 'flex';  // <-- flex, не block
      });

      // Закрытие модалки
      document.querySelector('#check-modal .close').addEventListener('click', () => {
        modal.style.display = 'none';
      });
      window.addEventListener('click', e => {
        if (e.target === modal) modal.style.display = 'none';
      });

      // Обработчик кнопок Редактировать/Удалить/Показать
      document.getElementById('checks-body').addEventListener('click', async e => {
        const id = e.target.dataset.id;
        if (e.target.matches('.show-results')) {
          loadResults(id);
        }
        else if (e.target.matches('.edit-check')) {
          const row = e.target.closest('tr');
          document.getElementById('modal-title').textContent = 'Редактировать инвентаризацию';
          document.getElementById('check-id').value    = id;
          document.getElementById('check-name').value  = row.children[1].textContent.trim();
          document.getElementById('check-start').value = row.children[2].textContent.trim();
          document.getElementById('check-end').value   = row.children[3].textContent.trim();
          modal.style.display = 'flex';
        }
        else if (e.target.matches('.delete-check')) {
          if (confirm('Удалить инвентаризацию?')) {
            await fetch(`${API}?action=deleteCheck&id=${id}`);
            loadChecks();
          }
        }
      });

      // Обработчик submit (добавление/редактирование)
      form.addEventListener('submit', async ev => {
        ev.preventDefault();
        const id    = document.getElementById('check-id').value;
        const name  = document.getElementById('check-name').value.trim();
        if (!name) return alert('Наименование обязательно');
        const start = document.getElementById('check-start').value;
        const end   = document.getElementById('check-end').value;
        const action = id ? 'updateCheck' : 'addCheck';

        const params = new URLSearchParams({ action, name, start_date: start, end_date: end });
        if (id) params.set('id', id);

        try {
          const res = await fetch(`${API}?${params.toString()}`);
          const json = await res.json();
          if (json.status === 'ok') {
            modal.style.display = 'none';
            loadChecks();
          } else {
            alert('Ошибка: ' + (json.message || 'неизвестная'));
          }
        } catch (err) {
          alert('Сетевая ошибка: ' + err);
        }
      });
    });

    // Функции загрузки списков и результатов (оставляем без изменений)
    async function loadChecks() {
      const res = await fetch(`${API}?action=getChecks`);
      const data = await res.json();
      document.getElementById('checks-body').innerHTML = data.map(c => `
        <tr>
          <td>${c.id}</td>
          <td>${c.name}</td>
          <td>${c.start_date||''}</td>
          <td>${c.end_date||''}</td>
          <td>
            <button class="edit-check"    data-id="${c.id}">✏️</button>
            <button class="delete-check"  data-id="${c.id}">🗑️</button>
          </td>
        </tr>
      `).join('');
    }
    async function loadResults(id) {
      // ...
    }
  </script>
</body>
</html>