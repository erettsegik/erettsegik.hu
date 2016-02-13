<?php

class user {

  protected $id        = null;
  protected $name      = null;
  protected $email     = null;
  protected $authority = null;

  public function __construct($id = null) {

    global $con;
    global $config;

    if ($id == null)
      return;

    try {

      $selectData = $con->prepare('select * from users where id = :id');
      $selectData->bindValue('id', $id, PDO::PARAM_INT);
      $selectData->execute();

      $userData = $selectData->fetch();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

    $this->id        = $userData['id'];
    $this->name      = $userData['name'];
    $this->email     = $userData['email'];
    $this->authority = $userData['authority'];

  }

  public function login($name, $password, $remember) {

    global $con;
    global $config;

    try {

      $getUserData = $con->prepare('
        select *
        from users
        where name = :name
      ');
      $getUserData->bindValue('name', $name, PDO::PARAM_STR);
      $getUserData->execute();

      $userData = $getUserData->fetch();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

    if (password_verify($password, $userData['password'])) {

      $_SESSION['userid'] = $userData['id'];
      $this->__construct($userData['id']);

      if ($remember) {

        $array = array('session' => mt_rand(), 'token' => mt_rand());

        $json = json_encode($array);

        setcookie('remember', $json, time()+2592000, '/', NULL, 0);

        try {

          $insert = $con->prepare('insert into logins values(DEFAULT, :userid, :session, :hash, now())');
          $insert->bindValue('userid', $userData['id'], PDO::PARAM_INT);
          $insert->bindValue('session', $array['session'], PDO::PARAM_STR);
          $insert->bindValue('hash', hash('sha256', $array['token']), PDO::PARAM_STR);
          $insert->execute();

        } catch(PDOException $e) {
          die($config['errors']['database']);
        }

      }

      return true;

    } else {

      return false;

    }

  }

  public function logout() {

    global $con;
    global $config;

    unset($_SESSION['userid']);

    if (isset($_COOKIE['remember'])) {

      setcookie('remember', '', time()-3600, '/', NULL, 0);

      $array = json_decode($_COOKIE['remember'], true);

      try {

        $delete = $con->prepare('delete from logins where session = :session');
        $delete->bindValue('session', $array['session'], PDO::PARAM_STR);
        $delete->execute();

      } catch (PDOException $e) {
        die($config['errors']['database']);
      }

    }

  }

  public function changePassword($oldPassword, $newPassword) {

    global $con;
    global $config;

    try {

      $selectData = $con->prepare('select password from users where id = :id');
      $selectData->bindValue('id', $this->id, PDO::PARAM_INT);
      $selectData->execute();

      $userData = $selectData->fetch();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

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

  private function checkAvailability($value, $type) {

    global $con;
    global $config;

      try {

        if ($type == 'name') {

          try {
            $select = $con->prepare('select id from users where name = :name');
            $select->bindValue('name', $value, PDO::PARAM_STR);
            $select->execute();
          } catch (PDOException $e) {
            die($config['errors']['database']);
          }

          return $select->rowCount();

        } else {

          try {
            $select = $con->prepare('select id from users where email = :email');
            $select->bindValue('email', $value, PDO::PARAM_STR);
            $select->execute();
          } catch (PDOException $e) {
            die($config['errors']['database']);
          }

          return $select->rowCount();

        }

      } catch (PDOException $e) {
        die($config['errors']['database']);
      }

  }

  public function register($name, $email, $authority, $password) {

    global $con;
    global $config;

    if (!ctype_alnum($name)) {
      $_SESSION['message'] = 'A neved csak számokat és az angol ABC betűit tartalmazhatja!';
      return false;
    }

    if (strlen($name) < 2 || strlen($name) > 20) {
      $_SESSION['message'] = 'A neved hossza 2 és 20 karakter közt lehet!';
      return false;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $_SESSION['message'] = 'Ez nem egy valódi e-mail cím!';
      return false;
    }

    if (strlen($password) < 6) {
      $_SESSION['message'] = 'A jelszavadnak legalább 6 karakter hosszúnak kell lennie!';
      return false;
    }

    if ($this->checkAvailability($name, 'name')) {
      $_SESSION['message'] = 'Ez a név már foglalt!';
      return false;
    }

    if ($this->checkAvailability($email, 'email')) {
      $_SESSION['message'] = 'Ezzel az e-mail címmel már létezik felhasználó!';
      return false;
    }

    try {

      $insertData = $con->prepare('
        insert into users
        values(DEFAULT, :name, :email, :authority, :password)
      ');
      $insertData->bindValue('name', $name, PDO::PARAM_STR);
      $insertData->bindValue('email', $email, PDO::PARAM_STR);
      $insertData->bindValue('authority', $authority, PDO::PARAM_INT);
      $insertData->bindValue(
        'password',
        password_hash($password, PASSWORD_DEFAULT),
        PDO::PARAM_STR
      );
      $insertData->execute();

      $this->id = $con->lastInsertId();

      $_SESSION['userid'] = $this->id;
      $this->__construct($this->id);
      return true;

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

    return false;

  }

  public function modifyData($name, $email, $authority, $password = '') {

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
        set name = :name, email = :email, authority = :authority
        where id = :id
      ');
      $updateData->bindValue('name', $name, PDO::PARAM_STR);
      $updateData->bindValue('email', $email, PDO::PARAM_STR);
      $updateData->bindValue('authority', $authority, PDO::PARAM_INT);
      $updateData->bindValue('id', $this->id, PDO::PARAM_INT);
      $updateData->execute();

    } catch (PDOException $e) {
      die($config['errors']['database'] . $e->getMessage());
    }

  }

  public function getData() {

    return array(
      'id'        => $this->id,
      'name'      => $this->name,
      'email'     => $this->email,
      'authority' => $this->authority,
    );

  }

}
