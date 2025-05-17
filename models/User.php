<?php
require_once '../connection.php';
class Users {
    public int $id;
    public string $username;
    public string $password;
    public string $role;
    public ?string $email;
    public string $last_name;
    public string $first_name;
    public ?string $middle_name;
    public ?string $phone;
    public ?string $address;

    public function __construct($id, $username, $password, $role, $email, $last_name, $first_name, $middle_name, $phone, $address) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
        $this->email = $email;
        $this->last_name = $last_name;
        $this->first_name = $first_name;
        $this->middle_name = $middle_name;
        $this->phone = $phone;
        $this->address = $address;
    }
}
?>
