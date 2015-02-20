<?php

class event {

  protected $id        = null;
  protected $name      = null;
  protected $startdate = null;
  protected $enddate   = null;

  public function __construct($id = null) {

    global $con;

    try {

      $selectData = $con->prepare('select * from events where id = :id');
      $selectData->bindValue('id', $id, PDO::PARAM_INT);
      $selectData->execute();

    } catch (PDOException $e) {
      die('Nem sikerült az osztály adatait betölteni.');
    }

    $subjectData = $selectData->fetch();

    $this->id        = $subjectData['id'];
    $this->name      = $subjectData['name'];
    $this->startdate = new DateTime($subjectData['startdate']);
    $this->enddate   = new DateTime($subjectData['enddate']);

  }

  public function insertData($name, $startdate, $enddate) {

    global $con;

    $this->name      = $name;
    $this->startdate = $startdate;
    $this->enddate   = $enddate;

    try {

      $insertData = $con->prepare('
        insert into events
        values(
          DEFAULT,
          :name,
          :startdate,
          :enddate
        )
      ');
      $insertData->bindValue('name', $this->name, PDO::PARAM_STR);
      $insertData->bindValue('startdate', $this->startdate, PDO::PARAM_STR);
      $insertData->bindValue('enddate', $this->enddate, PDO::PARAM_STR);
      $insertData->execute();

      $this->startdate = new DateTime($this->startdate);
      $this->enddate = new DateTime($this->enddate);

    } catch (PDOException $e) {
      die('Nem sikerült az esemény hozzáadása.');
    }

  }

  public function modifyData($name, $startdate, $enddate) {

    global $con;

    $this->name      = $name;
    $this->startdate = $startdate;
    $this->enddate   = $enddate;

    try {

      $modifyData = $con->prepare('
        update events
        set name = :name,
            startdate = :startdate,
            enddate = :enddate
        where id = :id
      ');
      $modifyData->bindValue('name', $this->name, PDO::PARAM_STR);
      $modifyData->bindValue('startdate', $this->startdate, PDO::PARAM_STR);
      $modifyData->bindValue('enddate', $this->enddate, PDO::PARAM_STR);
      $modifyData->bindValue('id', $this->id, PDO::PARAM_INT);
      $modifyData->execute();

      $this->startdate = new DateTime($this->startdate);
      $this->enddate = new DateTime($this->enddate);

    } catch (PDOException $e) {
      die('Nem sikerült az esemény módosítása.');
    }

  }

  public function remove() {

    global $con;

    try {

      $remove = $con->prepare('delete from events where id = :id');
      $remove->bindValue('id', $this->id, PDO::PARAM_INT);
      $remove->execute();

    } catch (PDOException $e) {
      die('Nem sikerült kitörölni az eseményt.');
    }

  }

  public function getData() {

    global $config;

    return array(
      'id'        => $this->id,
      'name'      => $this->name,
      'startdate' => $this->startdate->format($config['dateformat']),
      'enddate'   => $this->enddate->format($config['dateformat'])
    );

  }

}
