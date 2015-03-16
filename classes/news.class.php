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
    global $config;

    if ($id == null)
      return;

    try {

      $selectData = $con->prepare('select * from news where id = :id');
      $selectData->bindValue('id', $id, PDO::PARAM_INT);
      $selectData->execute();

      $newsData = $selectData->fetch();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

    $this->id         = $newsData['id'];
    $this->title      = $newsData['title'];
    $this->text       = $newsData['text'];
    $this->date       = new DateTime($newsData['date'], $config['tz']['utc']);
    $this->updatedate = ($newsData['updatedate'] != null)
                      ? new DateTime($newsData['updatedate'], $config['tz']['utc'])
                      : null;
    $this->creatorid  = $newsData['creatorid'];
    $this->live       = $newsData['live'];

    $this->date->setTimezone($config['tz']['local']);

    if ($this->updatedate != null) {
      $this->updatedate->setTimezone($config['tz']['local']);
    }

  }

  public function insertData($title, $text, $creatorid, $live) {

    global $con;
    global $config;

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
      die($config['errors']['database']);
    }

  }

  public function modifyData($title, $text, $live) {

    global $con;
    global $config;

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
      die($config['errors']['database']);
    }

  }

  public function remove() {

    global $con;
    global $config;

    try {

      $removeData = $con->prepare('
        delete from news
        where id = :id
      ');
      $removeData->bindValue('id', $this->id, PDO::PARAM_INT);
      $removeData->execute();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

  }

  public function getData($unsanitize = false) {

    global $config;

    return array(
       'id'         => $this->id,
       'title'      => $unsanitize ? $this->title : $this->title,
       'text'       => $unsanitize ? unprepareText($this->text) : $this->text,
       'date'       => $this->date->format($config['dateformat']),
       'updatedate' => $this->updatedate != null
                     ? $this->updatedate->format($config['dateformat'])
                     : null,
       'creatorid'  => $this->creatorid,
       'live'       => $this->live
    );

  }

}
