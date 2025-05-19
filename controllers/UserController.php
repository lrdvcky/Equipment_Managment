<?php
require_once '../connection.php';
require_once '../models/User.php';
require_once '../models/UsersContext.php';

class UserController {

    public static function index(): array {
        return UsersContext::getAllUsers();
    }

    public static function store(array $data): bool {
        $user = new User(
            0,
            $data['username'],
            $data['password'],
            $data['role'],
            $data['email'] ?? null,
            $data['last_name'],
            $data['first_name'],
            $data['middle_name'] ?? null,
            $data['phone'] ?? null,
            $data['address'] ?? null
        );
        return UsersContext::addUser($user);
    }

    public static function update(int $id, array $data): bool {
        $user = new User(
            $id,
            $data['username'],
            $data['password'],
            $data['role'],
            $data['email'] ?? null,
            $data['last_name'],
            $data['first_name'],
            $data['middle_name'] ?? null,
            $data['phone'] ?? null,
            $data['address'] ?? null
        );
        return UsersContext::updateUser($user);
    }

    public static function destroy(int $id): bool {
        return UsersContext::deleteUser($id);
    }
}
?>
