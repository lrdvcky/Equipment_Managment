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
        <h2 class="highlight">Инвентаризация оборудования</h2>
        <p>В этом разделе администратор может запускать новые инвентаризации, просматривать списки проверок и результаты.</p>
        <div class="equipment-controls">
            <input type="text" placeholder="Поиск по названию инвентаризации">
            <button id="add-check-btn" class="red-button">Добавить инвентаризацию</button>
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
        <h3 id="results-heading" style="margin-top:20px;">Результаты инвентаризации</h3>
        <div class="equipment-table">
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
        <!-- Модальное окно для добавления/редактирования -->
        <div id="check-modal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h3 id="modal-title">Новая инвентаризация</h3>
                <form id="check-form">
                    <input type="hidden" name="id" id="check-id" />
                    <label for="check-name">Наименование</label>
                    <input type="text" name="name" id="check-name" required />
                    <label for="check-start">Дата начала</label>
                    <input type="date" name="start_date" id="check-start" />
                    <label for="check-end">Дата окончания</label>
                    <input type="date" name="end_date" id="check-end" />
                    <button type="submit">Сохранить</button>
                </form>
            </div>
        </div>
    </main>
    <footer>
        &copy; 2025 Учебное заведение. Все права защищены.
    </footer>
</div>
<script>
    const API = '../controllers/InventoryCheckController.php';
    const modal = document.getElementById('check-modal');
    const form = document.getElementById('check-form');

    document.addEventListener('DOMContentLoaded', () => {
        loadChecks();
        // Открыть модал для добавления
        document.getElementById('add-check-btn').addEventListener('click', () => {
            form.reset();
            document.getElementById('modal-title').textContent = 'Новая инвентаризация';
            document.getElementById('check-id').value = '';
            modal.style.display = 'block';
        });
        // Обработчик кнопок в списке инвентаризаций
        document.getElementById('checks-body').addEventListener('click', async e => {
            const id = e.target.dataset.id;
            if (e.target.matches('.show-results')) {
                loadResults(id);
            }
            if (e.target.matches('.edit-check')) {
                const row = e.target.closest('tr');
                document.getElementById('modal-title').textContent = 'Редактировать инвентаризацию';
                document.getElementById('check-id').value = id;
                document.getElementById('check-name').value = row.children[1].textContent.trim();
                document.getElementById('check-start').value = row.children[2].textContent.trim();
                document.getElementById('check-end').value = row.children[3].textContent.trim();
                modal.style.display = 'block';
            }
            if (e.target.matches('.delete-check')) {
                if (confirm('Удалить инвентаризацию?')) {
                    await fetch(`${API}?action=deleteCheck&id=${id}`);
                    loadChecks();
                }
            }
        });
        // Отправка формы
        form.addEventListener('submit', async e => {
            e.preventDefault();
            const data = new URLSearchParams(new FormData(form));
            const action = document.getElementById('check-id').value ? 'updateCheck' : 'addCheck';
            await fetch(`${API}?action=${action}&${data}`);
            modal.style.display = 'none';
            loadChecks();
        });
        // Закрыть модал
        document.querySelector('#check-modal .close').addEventListener('click', () => {
            modal.style.display = 'none';
        });
        // Закрытие при клике вне содержимого
        window.addEventListener('click', e => {
            if (e.target === modal) modal.style.display = 'none';
        });
    });

    async function loadChecks() {
        const res = await fetch(`${API}?action=getChecks`);
        const checks = await res.json();
        const tb = document.getElementById('checks-body');
        tb.innerHTML = checks.map(c => `
            <tr>
                <td>${c.id}</td>
                <td>${c.name}</td>
                <td>${c.start_date || ''}</td>
                <td>${c.end_date || ''}</td>
                <td>
                    <button class="show-results" data-id="${c.id}">Оборудование</button>
                    <button class="edit-check" data-id="${c.id}">✏️</button>
                    <button class="delete-check" data-id="${c.id}">🗑️</button>
                </td>
            </tr>
        `).join('');
        if (checks.length) loadResults(checks[0].id);
    }

    async function loadResults(id) {
        document.getElementById('results-heading').textContent = `Результаты инвентаризации №${id}`;
        const res = await fetch(`${API}?action=getResults&id=${id}`);
        const data = await res.json();
        const tb = document.getElementById('results-body');
        tb.innerHTML = data.map(r => `
            <tr>
                <td>${r.equipment_name}</td>
                <td>${r.checked_by || '-'}</td>
                <td>${r.comment || ''}</td>
                <td>${r.status || ''}</td>
            </tr>
        `).join('');
    }

    function toggleMenu() {
        document.getElementById('mobileMenu').classList.toggle('open');
    }
</script>
</body>
</html>
