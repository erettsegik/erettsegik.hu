<?php

class category {

  protected $id   = null;
  protected $name = null;

  public function __construct($id = null) {

    global $con;

    try {

      $selectData = $con->prepare('select * from categories where id = :id');
      $selectData->bindValue('id', $id, PDO::PARAM_INT);
      $selectData->execute();

    } catch (PDOException $e) {
      die('Nem sikerült az osztály adatait betölteni.');
    }

    $categoryData = $selectData->fetch();

    $this->id   = $categoryData['id'];
    $this->name = $categoryData['name'];

  }

  public function insertData($name) {

    global $con;

    $this->name = $name;

    try {

      $insertData = $con->prepare('
        insert into categories
        values(
          DEFAULT,
          :name
        )
      ');
      $insertData->bindValue('name', $this->name, PDO::PARAM_STR);
      $insertData->execute();

    } catch (PDOException $e) {
      die('Nem sikerült a kategória hozzáadása.');
    }

  }

  public function modifyData($name) {

    global $con;

    if ($name == '') {

      $this->remove();

    } else {

      $this->name = $name;

      try {

        $insertData = $con->prepare('
          update categories
          set name = :name
          where id = :id
        ');
        $insertData->bindValue('name', $this->name, PDO::PARAM_STR);
        $insertData->bindValue('id', $this->id, PDO::PARAM_INT);
        $insertData->execute();

      } catch (PDOException $e) {
        die('Nem sikerült a kategória frissítése.');
      }

    }

  }

  public function remove() {

    global $con;

    try {

      $removeData = $con->prepare('
        delete from categories
        where id = :id
      ');
      $removeData->bindValue('id', $this->id, PDO::PARAM_INT);
      $removeData->execute();

    } catch (PDOException $e) {
      die('Nem sikerült a kategória törlése.');
    }

  }

  public function getData() {

    return array(
      'id'   => $this->id,
      'name' => $this->name
    );

  }

}
