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
}
