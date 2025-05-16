<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Авторизация | Учёт оборудования</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script>
        function redirectToMain(event) {
            event.preventDefault();
            window.location.href = "index.php";
        }
    </script>
</head>
<body>

<div class="wrapper">

    <header>
        <div class="header-content">
            <img src="img/logo.png" alt="Логотип" class="logo">
            <h1>Система учёта оборудования</h1>
        </div>
    </header>

    <main style="max-width: 400px; margin: 0 auto;">
        <h2 class="highlight">Вход в систему</h2>
        <p>Добро пожаловать! Пожалуйста, авторизуйтесь для входа.</p>

        <form onsubmit="redirectToMain(event)" class="auth-form">
            <label for="username">Логин:</label>
            <input type="text" id="username" name="username" required><br><br>

            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required><br><br>

            <button type="submit" class="red-button">Войти</button>
        </form>
    </main>

    <footer>
        &copy; 2025 Учебное заведение. Все права защищены.
    </footer>

</div>

</body>
</html>
