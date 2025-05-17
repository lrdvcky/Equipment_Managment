<?php
require_once '../admin/users.php';
require_once '../connection.php';

class UsersContext {
    public static function getAllUsers(): array {
        $users = [];
        $conn = OpenConnection();
        $sql = "SELECT * FROM User";
        $result = $conn->query($sql);

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new Users(
                $row['id'],
                $row['username'],
                $row['password'],
                $row['role'],
                $row['email'],
                $row['last_name'],
                $row['first_name'],
                $row['middle_name'],
                $row['phone'],
                $row['address']
            );
        }
        return $users;
    }
    public static function addUser(Users $user): void {
    $conn = OpenConnection();
    $stmt = $conn->prepare("INSERT INTO User (username, password, role, email, last_name, first_name, middle_name, phone, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $user->username,
        $user->password,
        $user->role,
        $user->email,
        $user->last_name,
        $user->first_name,
        $user->middle_name,
        $user->phone,
        $user->address
    ]);
}

public static function updateUser(Users $user): void {
    $conn = OpenConnection();
    $stmt = $conn->prepare("UPDATE User SET username = ?, password = ?, role = ?, email = ?, last_name = ?, first_name = ?, middle_name = ?, phone = ?, address = ? WHERE id = ?");
    $stmt->execute([
        $user->username,
        $user->password,
        $user->role,
        $user->email,
        $user->last_name,
        $user->first_name,
        $user->middle_name,
        $user->phone,
        $user->address,
        $user->id
    ]);
}

public static function deleteUser(int $id): void {
    $conn = OpenConnection();
    $stmt = $conn->prepare("DELETE FROM User WHERE id = ?");
    $stmt->execute([$id]);
}
}
