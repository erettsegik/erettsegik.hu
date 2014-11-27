<?php

class user {

    protected $id        = null;
    protected $name      = null;
    protected $authority = null;

    public function __construct($id = null) {

        global $con;

        try {

            $selectData = $con->prepare('select * from users where id = :id');
            $selectData->bindValue('id', $id, PDO::PARAM_INT);
            $selectData->execute();

            $newsData = $selectData->fetch();

        } catch (PDOException $e) {
            die('Nem sikerült a felhasználó betöltése.');
        }

        $this->id        = $newsData['id'];
        $this->name      = $newsData['name'];
        $this->authority = $newsData['authority'];

    }

    public function login($name, $password) {

        global $con;

        $getUserData = $con->prepare('
            select *
            from users
            where name = :name
        ');
        $getUserData->bindValue('name', $name, PDO::PARAM_STR);
        $getUserData->execute();

        $userData = $getUserData->fetch();

        if (password_verify($password, $userData['password'])) {

            $_SESSION['userid'] = $userData['id'];
            $this->__construct($userData['id']);
            return true;

        } else {

            return false;

        }

    }

    public function changePassword($oldPassword, $newPassword) {

        global $con;

        $selectData = $con->prepare('select password from users where id = :id');
        $selectData->bindValue('id', $this->id, PDO::PARAM_INT);
        $selectData->execute();

        $userData = $selectData->fetch();

        if (password_verify($oldPassword, $userData['password'])) {

            try {

                $updatePassword = $con->prepare('update users set password = :password where id = :id');
                $updatePassword->bindValue('password', password_hash($newPassword, PASSWORD_DEFAULT), PDO::PARAM_STR);
                $updatePassword->bindValue('id', $this->id, PDO::PARAM_INT);
                $updatePassword->execute();

            } catch (PDOException $e) {
                die('Nem sikerült a jelszóváltoztatás.');
            }

        } else {
            return false;
        }

        return true;

    }

    public function getData() {

        return array(
            'id' => $this->id,
            'name' => $this->name,
            'authority' => $this->authority
        );

    }

}
