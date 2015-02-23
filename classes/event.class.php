<?php

class event {

  protected $id        = null;
  protected $name      = null;
  protected $startdate = null;
  protected $enddate   = null;

  public function __construct($id = null) {

    global $con;
    global $config;

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
    $this->startdate = new DateTime($subjectData['startdate'], $config['tz']['utc']);
    $this->enddate   = new DateTime($subjectData['enddate'], $config['tz']['utc']);

    $this->startdate->setTimezone($config['tz']['local']);
    $this->enddate->setTimezone($config['tz']['local']);

  }

  public function insertData($name, $startdate, $enddate) {

    global $con;
    global $config;

    $this->name      = $name;
    $this->startdate = new DateTime($startdate, $config['tz']['local']);
    $this->enddate   = new DateTime($enddate, $config['tz']['local']);

    $this->startdate->setTimezone($config['tz']['utc']);
    $this->enddate->setTimezone($config['tz']['utc']);

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

    $this->startdate->setTimezone($config['tz']['local']);
    $this->enddate->setTimezone($config['tz']['local']);

  }

  public function modifyData($name, $startdate, $enddate) {

    global $con;
    global $config;

    $this->name      = $name;
    $this->startdate = new DateTime($startdate, $config['tz']['local']);
    $this->enddate   = new DateTime($enddate, $config['tz']['local']);

    $this->startdate->setTimezone($config['tz']['utc']);
    $this->enddate->setTimezone($config['tz']['utc']);

    try {

      $modifyData = $con->prepare('
        update events
        set name = :name,
            startdate = :startdate,
            enddate = :enddate
        where id = :id
      ');
      $modifyData->bindValue('name', $this->name, PDO::PARAM_STR);
      $modifyData->bindValue('startdate', $this->startdate->format($config['htmldate']), PDO::PARAM_STR);
      $modifyData->bindValue('enddate', $this->enddate->format($config['htmldate']), PDO::PARAM_STR);
      $modifyData->bindValue('id', $this->id, PDO::PARAM_INT);
      $modifyData->execute();

    } catch (PDOException $e) {
      die('Nem sikerült az esemény módosítása.');
    }

    $this->startdate->setTimezone($config['tz']['local']);
    $this->enddate->setTimezone($config['tz']['local']);

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
      'id'            => $this->id,
      'name'          => $this->name,
      'startdate'     => $htmlformat ? $this->startdate->format($config['htmldate']) : $this->startdate->format($config['dateformat']),
      'enddate'       => $htmlformat ? $this->enddate->format($config['htmldate']) : $this->enddate->format($config['dateformat']),
      'startdatetext' => getDateText($this->startdate),
      'enddatetext'   => getDateText($this->enddate)
    );

  }

}
