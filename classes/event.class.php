<?php

class event {

  protected $id        = null;
  protected $name      = null;
  protected $startdate = null;
  protected $enddate   = null;

  protected $utc;
  protected $bud;

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

    $this->utc = new DateTimeZone('UTC');
    $this->bud = new DateTimeZone('Europe/Budapest');

    $this->id        = $subjectData['id'];
    $this->name      = $subjectData['name'];
    $this->startdate = new DateTime($subjectData['startdate'], $this->utc);
    $this->enddate   = new DateTime($subjectData['enddate'], $this->utc);

    $this->startdate->setTimezone($this->bud);
    $this->enddate->setTimezone($this->bud);

  }

  public function insertData($name, $startdate, $enddate) {

    global $con;
    global $config;

    $this->name      = $name;
    $this->startdate = new DateTime($startdate, $this->bud);
    $this->enddate   = new DateTime($enddate, $this->bud);

    $this->startdate->setTimezone($this->utc);
    $this->enddate->setTimezone($this->utc);

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
      $insertData->bindValue('startdate', $this->startdate->format($config['htmldate']), PDO::PARAM_STR);
      $insertData->bindValue('enddate', $this->enddate->format($config['htmldate']), PDO::PARAM_STR);
      $insertData->execute();

    } catch (PDOException $e) {
      die('Nem sikerült az esemény hozzáadása.');
    }

    $this->startdate->setTimezone($this->bud);
    $this->enddate->setTimezone($this->bud);

  }

  public function modifyData($name, $startdate, $enddate) {

    global $con;

    $this->name      = $name;
    $this->startdate = new DateTime($startdate, $this->bud);
    $this->enddate   = new DateTime($enddate, $this->bud);

    $this->startdate->setTimezone($this->utc);
    $this->enddate->setTimezone($this->utc);

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

    $this->startdate->setTimezone($this->bud);
    $this->enddate->setTimezone($this->bud);

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

  public function getData($htmlformat = false) {

    global $config;

    return array(
      'id'        => $this->id,
      'name'      => $this->name,
      'startdate' => ($htmlformat) ? $this->startdate->format($config['htmldate']) : $this->startdate->format($config['dateformat']),
      'enddate'   => ($htmlformat) ? $this->enddate->format($config['htmldate']) : $this->enddate->format($config['dateformat'])
    );

  }

}
