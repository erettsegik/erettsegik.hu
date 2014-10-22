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

        if ($userData['password'] == $password) {

            $_SESSION['userid'] = $userData['id'];
            $this->__construct($userData['id']);
            return true;

        } else {

            return false;

        }

    }

    public function getData() {

        return array(
            'id' => $this->id,
            'name' => $this->name,
            'authority' => $this->authority
        );

    }

}
