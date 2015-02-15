<?php

class news {

  protected $id         = null;
  protected $title      = null;
  protected $text       = null;
  protected $date       = null;
  protected $updatedate = null;
  protected $creatorid  = null;
  protected $live       = null;

  public function __construct($id = null) {

    global $con;

    try {

      $selectData = $con->prepare('select * from news where id = :id');
      $selectData->bindValue('id', $id, PDO::PARAM_INT);
      $selectData->execute();

      $newsData = $selectData->fetch();

    } catch (PDOException $e) {
      die('Nem sikerült a hír betöltése.');
    }

    $this->id         = $newsData['id'];
    $this->title      = $newsData['title'];
    $this->text       = $newsData['text'];
    $this->date       = new DateTime($newsData['date']);
    $this->updatedate = ($newsData['updatedate'] != null)
                      ? new DateTime($newsData['updatedate'])
                      : null;
    $this->creatorid  = $newsData['creatorid'];
    $this->live       = $newsData['live'];

  }

  public function insertData($title, $text, $creatorid, $live) {

    global $con;

    $this->title     = $title;
    $this->text      = $text;
    $this->live      = $live;
    $this->creatorid = $creatorid;

    try {

      $insertData = $con->prepare('
        insert into news
        values(
          DEFAULT,
          :title,
          :text,
          DEFAULT,
          DEFAULT,
          :creatorid,
          :live
        )
      ');
      $insertData->bindValue('title', $this->title, PDO::PARAM_STR);
      $insertData->bindValue('text', $this->text, PDO::PARAM_STR);
      $insertData->bindValue('creatorid', $this->creatorid, PDO::PARAM_INT);
      $insertData->bindValue('live', $this->live, PDO::PARAM_INT);
      $insertData->execute();

    } catch (PDOException $e) {
      die('Nem sikerült a hír hozzáadása.');
    }

  }

  public function modifyData($title, $text, $live) {

    global $con;

    $this->title = $title;
    $this->text  = $text;
    $this->live  = $live;

    try {

      $insertData = $con->prepare('
        update news
        set title = :title,
          text = :text,
          updatedate = now(),
          live = :live
        where id = :id
      ');
      $insertData->bindValue('title', $this->title, PDO::PARAM_STR);
      $insertData->bindValue('text', $this->text, PDO::PARAM_STR);
      $insertData->bindValue('live', $this->live, PDO::PARAM_INT);
      $insertData->bindValue('id', $this->id, PDO::PARAM_INT);
      $insertData->execute();

    } catch (PDOException $e) {
      die('Nem sikerült a hír frissítése.');
    }

  }

  public function remove() {

    global $con;

    try {

      $removeData = $con->prepare('
        delete from news
        where id = :id
      ');
      $removeData->bindValue('id', $this->id, PDO::PARAM_INT);
      $removeData->execute();

    } catch (PDOException $e) {
      die('Nem sikerült a hír törlése.');
    }

  }

  public function getData($unsanitize = false) {

    global $config;

    return array(
       'id'         => $this->id,
       'title'      => ($unsanitize) ? unprepareText($this->title) : $this->title,
       'text'       => ($unsanitize) ? unprepareText($this->text) : $this->text,
       'date'       => $this->date->format($config['dateformat']),
       'updatedate' => ($this->updatedate != null)
                     ? $this->updatedate->format($config['dateformat'])
                     : null,
       'creatorid'  => $this->creatorid,
       'live'       => $this->live
    );

  }

}
