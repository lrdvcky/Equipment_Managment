<?php
// models/UsersContext.php

require_once __DIR__ . '/../connection.php';
require_once __DIR__ . '/User.php';

class UsersContext {

    /**
     * Возвращает массив User
     * @return User[]
     */
    public static function getAllUsers() {
        $users = [];
        $conn = OpenConnection();
        $stmt = $conn->query("SELECT * FROM `User` ORDER BY last_name, first_name");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User(
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

    /**
     * Возвращает полное ФИО пользователя по его id
     * @param int $id
     * @return string
     */
    public static function getFullNameById($id) {
        $conn = OpenConnection();
        $stmt = $conn->prepare("
            SELECT last_name, first_name, middle_name
            FROM `User`
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$r) {
            return '';
        }
        $name = $r['last_name'] . ' ' . $r['first_name'];
        if (!empty($r['middle_name'])) {
            $name .= ' ' . $r['middle_name'];
        }
        return trim($name);
    }

    /**
     * Удаляет пользователя по ID
     * @param int $id
     */
    public static function deleteUser($id) {
        $conn = OpenConnection();
        $stmt = $conn->prepare("DELETE FROM `User` WHERE id = ?");
        $stmt->execute([$id]);
    }

    /**
     * Создаёт нового пользователя (пароль храним в открытом виде)
     * @param array $data
     * @return int новый ID
     * @throws Exception если роль неверна
     */
    public static function createUser($data) {
        $map = [
            'Администратор'      => 'admin',
            'Ответственное лицо' => 'teacher',
            'Пользователь'       => 'staff',
            'admin'   => 'admin',
            'teacher' => 'teacher',
            'staff'   => 'staff',
        ];
        $rawRole = trim($data['role'] ?? '');
        if (!isset($map[$rawRole])) {
            throw new Exception("Недопустимая роль: {$rawRole}");
        }
        $role = $map[$rawRole];

        $conn = OpenConnection();
        $stmt = $conn->prepare("
            INSERT INTO `User`
              (username, password, role, email, last_name, first_name, middle_name, phone, address)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['username'],
            $data['password'],
            $role,
            $data['email']       ?? null,
            $data['last_name'],
            $data['first_name'],
            $data['middle_name'] ?? null,
            $data['phone']       ?? null,
            $data['address']     ?? null
        ]);
        return $conn->lastInsertId();
    }

    /**
     * Обновляет данные пользователя
     * @param int   $id
     * @param array $data
     * @throws Exception если роль неверна
     */
    public static function updateUser($id, $data) {
        $map = [
            'Администратор'      => 'admin',
            'Ответственное лицо' => 'teacher',
            'Пользователь'       => 'staff',
            'admin'   => 'admin',
            'teacher' => 'teacher',
            'staff'   => 'staff',
        ];
        $rawRole = trim($data['role'] ?? '');
        if (!isset($map[$rawRole])) {
            throw new Exception("Недопустимая роль: {$rawRole}");
        }
        $role = $map[$rawRole];

        $conn   = OpenConnection();
        $fields = [];
        $params = [];

        // логин
        $fields[] = "username = ?";
        $params[] = $data['username'];

        // пароль (если передан)
        if (!empty($data['password'])) {
            $fields[] = "password = ?";
            $params[] = $data['password'];
        }

        // роль
        $fields[] = "role = ?";
        $params[] = $role;

        // остальные поля
        $fields[] = "email = ?";
        $params[] = $data['email']       ?? null;
        $fields[] = "last_name = ?";
        $params[] = $data['last_name'];
        $fields[] = "first_name = ?";
        $params[] = $data['first_name'];
        $fields[] = "middle_name = ?";
        $params[] = $data['middle_name'] ?? null;
        $fields[] = "phone = ?";
        $params[] = $data['phone']       ?? null;
        $fields[] = "address = ?";
        $params[] = $data['address']     ?? null;

        // условие WHERE
        $params[] = $id;
        $sql = "UPDATE `User` SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
    }
    /** @return User|null */
public static function findByUsername(string $login) {
  $conn = OpenConnection();
  $stmt = $conn->prepare("SELECT * FROM `User` WHERE username = ?");
  $stmt->execute([$login]);
  $r = $stmt->fetch(PDO::FETCH_ASSOC);
  return $r
    ? new User($r['id'],$r['username'],$r['password'],$r['role'],$r['email'],$r['last_name'],$r['first_name'],$r['middle_name'],$r['phone'],$r['address'])
    : null;
}

}
