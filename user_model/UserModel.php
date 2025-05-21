<?php
class UserModel {
    public $id;
    public $full_name;
    public $username;
    public $email;
    public $phone;
    public $address;
    public $role;

    public function __construct($data) {
        $this->id = $data['id'];
        $this->full_name = trim("{$data['last_name']} {$data['first_name']} {$data['middle_name']}");
        $this->username = $data['username'];
        $this->email = $data['email'];
        $this->phone = $data['phone'];
        $this->address = $data['address'];
        $this->role = $data['role'];
    }
}
?>
