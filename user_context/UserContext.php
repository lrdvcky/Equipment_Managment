<?php
require_once '../connection.php';
require_once '../user_model/UserModel.php';

class UserContext {
    public static function getUserById($userId) {
        $pdo = OpenConnection();
        $stmt = $pdo->prepare("SELECT * FROM User WHERE id = ?");
        $stmt->execute([$userId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new UserModel($data) : null;
    }
}
?>
