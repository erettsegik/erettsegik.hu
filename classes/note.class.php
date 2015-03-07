<?php

require_once 'subject.class.php';

class note {

  protected $id          = null;
  protected $title       = null;
  protected $text        = null;
  protected $subject     = null;
  protected $category    = null;
  protected $updatedate  = null;
  protected $ordernumber = null;
  protected $live        = null;
  protected $incomplete  = null;

  public function __construct($id = null) {

    global $con;
    global $config;

    try {

      $selectData = $con->prepare('select * from notes where id = :id');
      $selectData->bindValue('id', $id, PDO::PARAM_INT);
      $selectData->execute();

      $noteData = $selectData->fetch();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

    $this->id          = $noteData['id'];
    $this->title       = $noteData['title'];
    $this->text        = $noteData['text'];
    $this->subject     = new subject($noteData['subjectid']);
    $this->category    = $noteData['category'];
    $this->updatedate  = new DateTime($noteData['updatedate'], $config['tz']['utc']);
    $this->ordernumber = $noteData['ordernumber'];
    $this->live        = $noteData['live'];
    $this->incomplete  = $noteData['incomplete'];

    $this->updatedate->setTimezone($config['tz']['local']);

  }

  public function insertData($title, $text, $subjectid, $category, $live, $incomplete) {

    global $con;
    global $config;

    $this->title      = $title;
    $this->text       = $text;
    $this->subject    = new subject($subjectid);
    $this->category   = $category;
    $this->live       = $live;
    $this->incomplete = $incomplete;

    try {

      $insertData = $con->prepare('
        insert into notes
        values(
          DEFAULT,
          :title,
          :text,
          :subjectid,
          :category,
          DEFAULT,
          0,
          :live,
          :incomplete
        )
      ');
      $insertData->bindValue('title', $this->title, PDO::PARAM_STR);
      $insertData->bindValue('text', $this->text, PDO::PARAM_STR);
      $insertData->bindValue('subjectid', $subjectid, PDO::PARAM_INT);
      $insertData->bindValue('category', $this->category, PDO::PARAM_INT);
      $insertData->bindValue('live', $this->live, PDO::PARAM_INT);
      $insertData->bindValue('incomplete', $this->incomplete, PDO::PARAM_INT);
      $insertData->execute();

      $this->id = $con->lastInsertId();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

  }

  public function modifyData($title, $text, $subjectid, $category, $live, $incomplete) {

    global $con;
    global $config;

    $this->title      = $title;
    $this->text       = $text;
    $this->subject    = new subject($subjectid);
    $this->category   = $category;
    $this->live       = $live;
    $this->incomplete = $incomplete;

    try {

      $modifyData = $con->prepare('
        update notes
        set title = :title,
          text = :text,
          subjectid = :subjectid,
          category = :category,
          live = :live,
          incomplete = :incomplete
        where id = :id
      ');
      $modifyData->bindValue('title', $this->title, PDO::PARAM_STR);
      $modifyData->bindValue('text', $this->text, PDO::PARAM_STR);
      $modifyData->bindValue('subjectid', $subjectid, PDO::PARAM_INT);
      $modifyData->bindValue('category', $this->category, PDO::PARAM_INT);
      $modifyData->bindValue('live', $this->live, PDO::PARAM_INT);
      $modifyData->bindValue('incomplete', $this->incomplete, PDO::PARAM_INT);
      $modifyData->bindValue('id', $this->id, PDO::PARAM_INT);
      $modifyData->execute();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

  }

  public function modifyOrder($ordernumber) {

    global $con;
    global $config;

    $this->ordernumber = $ordernumber;

    try {

      $modifyData = $con->prepare('
        update notes
        set ordernumber = :ordernumber
        where id = :id
      ');
      $modifyData->bindValue('ordernumber', $this->ordernumber, PDO::PARAM_INT);
      $modifyData->bindValue('id', $this->id, PDO::PARAM_INT);
      $modifyData->execute();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

  }

  public function remove() {

    global $con;
    global $config;

    try {

      $remove = $con->prepare('delete from notes where id = :id');
      $remove->bindValue('id', $this->id, PDO::PARAM_INT);
      $remove->execute();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

  }

  public function getData($unsanitize = false) {

    global $config;

    return array(
      'id'          => $this->id,
      'title'       => $unsanitize ? $this->title : $this->title,
      'text'        => $unsanitize ? unprepareText($this->text) : $this->text,
      'subjectid'   => isset($this->subject) ? $this->subject->getData()['id'] : null,
      'category'    => $this->category,
      'updatedate'  => isset($this->updatedate) ? $this->updatedate->format($config['dateformat']) : null,
      'ordernumber' => $this->ordernumber,
      'live'        => $this->live,
      'incomplete'  => $this->incomplete
    );

  }

}
