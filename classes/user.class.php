<?php

class user {

  protected $id        = null;
  protected $name      = null;
  protected $authority = null;

  public function __construct($id = null) {

    global $con;
    global $config;

    try {

      $selectData = $con->prepare('select * from users where id = :id');
      $selectData->bindValue('id', $id, PDO::PARAM_INT);
      $selectData->execute();

      $newsData = $selectData->fetch();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

    $this->id        = $newsData['id'];
    $this->name      = $newsData['name'];
    $this->authority = $newsData['authority'];

  }

  public function login($name, $password) {

    global $con;
    global $config;

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
    global $config;

    $selectData = $con->prepare('select password from users where id = :id');
    $selectData->bindValue('id', $this->id, PDO::PARAM_INT);
    $selectData->execute();

    $userData = $selectData->fetch();

    if (password_verify($oldPassword, $userData['password'])) {

      try {

        $updatePassword = $con->prepare('
          update users
          set password = :password
          where id = :id
        ');
        $updatePassword->bindValue(
          'password',
          password_hash($newPassword, PASSWORD_DEFAULT),
          PDO::PARAM_STR
        );
        $updatePassword->bindValue('id', $this->id, PDO::PARAM_INT);
        $updatePassword->execute();

      } catch (PDOException $e) {
        die($config['errors']['database']);
      }

    } else {
      return false;
    }

    return true;

  }

  public function register($name, $authority, $password) {

    global $con;
    global $config;

    try {

      $insertData = $con->prepare('
        insert into users
        values(DEFAULT, :name, :authority, :password)
      ');
      $insertData->bindValue('name', $name, PDO::PARAM_STR);
      $insertData->bindValue('authority', $authority, PDO::PARAM_INT);
      $insertData->bindValue(
        'password',
        password_hash($password, PASSWORD_DEFAULT),
        PDO::PARAM_STR
      );
      $insertData->execute();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

  }

  public function modifyData($name, $authority, $password) {

    global $con;
    global $config;

    if ($password != '') {

      try {

        $updatePassword = $con->prepare('
          update users
          set password = :password
          where id = :id
        ');
        $updatePassword->bindValue(
          'password',
          password_hash($password, PASSWORD_DEFAULT),
          PDO::PARAM_STR
        );
        $updatePassword->bindValue('id', $this->id, PDO::PARAM_INT);
        $updatePassword->execute();

      } catch (PDOException $e) {
        die($config['errors']['database']);
      }

    }

    try {

      $updateData = $con->prepare('
        update users
        set name = :name, authority = :authority
        where id = :id
      ');
      $updateData->bindValue('name', $name, PDO::PARAM_STR);
      $updateData->bindValue('authority', $authority, PDO::PARAM_INT);
      $updateData->bindValue('id', $this->id, PDO::PARAM_INT);
      $updateData->execute();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

  }

  public function getData() {

    return array(
      'id'        => $this->id,
      'name'      => $this->name,
      'authority' => $this->authority
    );

  }

}
