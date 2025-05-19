<?php
require_once '../connection.php';
require_once '../models/User.php';
require_once '../models/UsersContext.php';

class UserController {
    public static function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $user = new Users(
                $_POST['id'] ?? 0,
                $_POST['username'],
                $_POST['password'],
                $_POST['role'],
                $_POST['email'],
                $_POST['last_name'],
                $_POST['first_name'],
                $_POST['middle_name'],
                $_POST['phone'],
                $_POST['address']
            );

            switch ($_POST['action']) {
                case 'add':
                    UsersContext::addUser($user);
                    break;
                case 'update':
                    UsersContext::updateUser($user);
                    break;
                case 'delete':
                    UsersContext::deleteUser($user->id);
                    break;
            }

            header('Location: users.php');
            exit();
        }
    }
}
?>
