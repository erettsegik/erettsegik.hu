<?php

class event {

  protected $id        = null;
  protected $name      = null;
  protected $startdate = null;
  protected $enddate   = null;

  public function __construct($id = null) {

    global $con;
    global $config;

    if ($id == null)
      return;

    try {

      $selectData = $con->prepare('select * from events where id = :id');
      $selectData->bindValue('id', $id, PDO::PARAM_INT);
      $selectData->execute();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

    $eventData = $selectData->fetch();

    $this->id        = $eventData['id'];
    $this->name      = $eventData['name'];
    $this->startdate = new DateTime($eventData['startdate'], $config['tz']['utc']);
    $this->enddate   = new DateTime($eventData['enddate'], $config['tz']['utc']);

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
      die($config['errors']['database']);
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
      die($config['errors']['database']);
    }

    $this->startdate->setTimezone($config['tz']['local']);
    $this->enddate->setTimezone($config['tz']['local']);

  }

  public function remove() {

    global $con;
    global $config;

    try {

      $remove = $con->prepare('delete from events where id = :id');
      $remove->bindValue('id', $this->id, PDO::PARAM_INT);
      $remove->execute();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

  }

  public function getProgress() {

    global $config;

    $current = new DateTime(null, $config['tz']['utc']);
    $current->setTimeZone($config['tz']['local']);

    $length = $this->enddate->getTimestamp() - $this->startdate->getTimestamp();

    $result = 100*($current->getTimestamp() - $this->startdate->getTimestamp()) / $length;

    return $result;

  }

  public function getData($htmlformat = false) {

    global $config;

    return array(
      'id'            => $this->id,
      'name'          => $this->name,
      'startdate'     => $htmlformat ? $this->startdate->format($config['htmldate']) : $this->startdate->format($config['dateformat']),
      'enddate'       => $htmlformat ? $this->enddate->format($config['htmldate']) : $this->enddate->format($config['dateformat']),
      'startdatetext' => getDateText($this->startdate),
      'enddatetext'   => getDateText($this->enddate),
      'progress'      => $this->getProgress()
    );

  }

}
