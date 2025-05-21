<?php
require_once __DIR__ . '/../connection.php';
require_once __DIR__ . '/User.php';

class UsersContext {
    /**
     * Возвращает массив User
     */
    public static function getAllUsers(): array {
        $users = [];
        $conn = OpenConnection();
        $sql  = "SELECT * FROM `User`";
        $stmt = $conn->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User(
                $row['id'],
                $row['username'],
                $row['password'],  // теперь в открытом виде
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
     * Удаляет пользователя по ID
     */
    public static function deleteUser(int $id): void {
        $conn = OpenConnection();
        $stmt = $conn->prepare("DELETE FROM `User` WHERE id = ?");
        $stmt->execute([$id]);
    }

    /**
     * Создаёт нового пользователя (пароль сохраняется в открытом виде).
     * @return int — новый ID
     * @throws Exception — если роль неверна
     */
    public static function createUser(array $data): int {
        // --- 1. Маппинг и валидация роли ---
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

        // --- 2. Вставка в БД (пароль не хешируем) ---
        $conn = OpenConnection();
        $stmt = $conn->prepare("
            INSERT INTO `User`
              (username, password, role, email, last_name, first_name, middle_name, phone, address)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['username'],
            $data['password'],      // СЫРОЙ пароль
            $role,
            $data['email']       ?? null,
            $data['last_name'],
            $data['first_name'],
            $data['middle_name'] ?? null,
            $data['phone']       ?? null,
            $data['address']     ?? null
        ]);

        return (int)$conn->lastInsertId();
    }

    /**
     * Обновляет данные пользователя.
     * Если передан непустой пароль — обновляет его (также без хеширования).
     * @throws Exception — если роль неверна
     */
    public static function updateUser(int $id, array $data): void {
        // --- 1. Маппинг роли ---
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

        // --- 2. Построение SQL ---
        $conn   = OpenConnection();
        $fields = [];
        $params = [];

        // логин
        $fields[] = "username = ?";
        $params[] = $data['username'];

        // пароль (если передан непустой)
        if (!empty($data['password'])) {
            $fields[] = "password = ?";
            $params[] = $data['password'];  // СЫРОЙ пароль
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

        // ID
        $params[] = $id;
        $sql = "UPDATE `User` SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
    }
}
