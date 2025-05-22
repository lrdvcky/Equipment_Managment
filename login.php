<?php
session_start();
require_once 'connection.php';

$error = '';
$pdo = OpenConnection(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT * FROM User WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['password'] === $password) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: users/index.php");
            }
            exit();
        } else {
            $error = "Неверный логин или пароль";
        }
    } else {
        $error = "Введите логин и пароль";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Авторизация | Учёт оборудования</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <style>
        h2.highlight {
            color: red;
            text-align: center;
            margin-top: 0;
        }
        p {
            text-align: center;
            margin-bottom: 20px;
        }
        .auth-form {
            display: flex;
            flex-direction: column;
        }
        input {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
            box-sizing: border-box;
        }
        .red-button {
            background-color: red;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 1em;
        }
        .red-button:hover {
            background-color: #c9302c;
        }
        .error {
            text-align: center;
        }
    </style>
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

        <?php if (!empty($error)): ?>
            <div style="color: red; margin-bottom: 15px;" class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
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
