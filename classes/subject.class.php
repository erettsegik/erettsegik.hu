<?php

class subject {

  protected $id        = null;
  protected $name      = null;
  protected $mandatory = null;

  public function __construct($id = null) {

    global $con;

    try {

      $selectData = $con->prepare('select * from subjects where id = :id');
      $selectData->bindValue('id', $id, PDO::PARAM_INT);
      $selectData->execute();

      $subjectData = $selectData->fetch();

    } catch (PDOException $e) {
      die('Nem sikerült a tárgy betöltése.');
    }

    $this->id        = $subjectData['id'];
    $this->name      = $subjectData['name'];
    $this->mandatory = $subjectData['mandatory'];

  }

  public function insertData($name, $mandatory) {

    global $con;

    $this->name      = $name;
    $this->mandatory = $mandatory;

    try {

      $insertData = $con->prepare('
        insert into subjects
        values(
          DEFAULT,
          :name,
          :mandatory
        )
      ');
      $insertData->bindValue('name', $this->name, PDO::PARAM_STR);
      $insertData->bindValue('mandatory', $this->mandatory, PDO::PARAM_INT);
      $insertData->execute();

    } catch (PDOException $e) {
      die('Nem sikerült a tárgy hozzáadása.');
    }

  }

  public function modifyData($name, $mandatory) {

    global $con;

    if ($name == '') {

      $this->remove();

    } else {

      $this->name      = $name;
      $this->mandatory = $mandatory;

      try {

        $insertData = $con->prepare('
          update subjects
          set name = :name,
            mandatory = :mandatory
          where id = :id
        ');
        $insertData->bindValue('name', $this->name, PDO::PARAM_STR);
        $insertData->bindValue('mandatory', $this->mandatory, PDO::PARAM_INT);
        $insertData->bindValue('id', $this->id, PDO::PARAM_INT);
        $insertData->execute();

      } catch (PDOException $e) {
        die('Nem sikerült a tárgy frissítése.');
      }

    }

  }

  public function remove() {

    global $con;

    try {

      $removeSubject = $con->prepare('
        delete from subjects
        where id = :id
      ');
      $removeSubject->bindValue('id', $this->id, PDO::PARAM_INT);
      $removeSubject->execute();

    } catch (PDOException $e) {
      die('Nem sikerült a tárgy törlése.');
    }

  }

  public function getData() {

    return array(
      'id'        => $this->id,
      'name'      => $this->name,
      'mandatory' => $this->mandatory
    );

  }

}
