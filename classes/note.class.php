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

  public function __construct($id = null) {

    global $con;

    try {

      $selectData = $con->prepare('select * from notes where id = :id');
      $selectData->bindValue('id', $id, PDO::PARAM_INT);
      $selectData->execute();

      $noteData = $selectData->fetch();

    } catch (PDOException $e) {
      die('Nem sikerült a jegyzet betöltése.');
    }

    $this->id          = $noteData['id'];
    $this->title       = $noteData['title'];
    $this->text        = $noteData['text'];
    $this->subject     = new subject($noteData['subjectid']);
    $this->category    = $noteData['category'];
    $this->updatedate  = new DateTime($noteData['updatedate']);
    $this->ordernumber = $noteData['ordernumber'];
    $this->live        = $noteData['live'];

  }

  public function insertData($title, $text, $subjectid, $category, $live) {

    global $con;

    $this->title    = $title;
    $this->text     = $text;
    $this->subject  = new subject($subjectid);
    $this->category = $category;
    $this->live     = $live;

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
          :live
        )
      ');
      $insertData->bindValue('title', $this->title, PDO::PARAM_STR);
      $insertData->bindValue('text', $this->text, PDO::PARAM_STR);
      $insertData->bindValue('subjectid', $subjectid, PDO::PARAM_INT);
      $insertData->bindValue('category', $this->category, PDO::PARAM_INT);
      $insertData->bindValue('live', $this->live, PDO::PARAM_INT);
      $insertData->execute();

      $this->id = $con->lastInsertId();

    } catch (PDOException $e) {
      die('Nem sikerült elmenteni a jegyzetet.');
    }

  }

  public function modifyData($title, $text, $subjectid, $category, $live) {

    global $con;

    $this->title    = $title;
    $this->text     = $text;
    $this->subject  = new subject($subjectid);
    $this->category = $category;
    $this->live     = $live;

    try {

      $modifyData = $con->prepare('
        update notes
        set title = :title,
          text = :text,
          subjectid = :subjectid,
          category = :category,
          live = :live
        where id = :id
      ');
      $modifyData->bindValue('title', $this->title, PDO::PARAM_STR);
      $modifyData->bindValue('text', $this->text, PDO::PARAM_STR);
      $modifyData->bindValue('subjectid', $subjectid, PDO::PARAM_INT);
      $modifyData->bindValue('category', $this->category, PDO::PARAM_INT);
      $modifyData->bindValue('live', $this->live, PDO::PARAM_INT);
      $modifyData->bindValue('id', $this->id, PDO::PARAM_INT);
      $modifyData->execute();

    } catch (PDOException $e) {
      die('Nem sikerült a jegyzetet frissíteni.');
    }

  }

  public function modifyOrder($ordernumber) {

    global $con;

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
      die('Nem sikerült a jegyzetet frissíteni.');
    }

  }

  public function remove() {

    global $con;

    try {

      $remove = $con->prepare('delete from notes where id = :id');
      $remove->bindValue('id', $this->id, PDO::PARAM_INT);
      $remove->execute();

    } catch (PDOException $e) {
      die('Nem sikerült kitörölni a jegyzetet.');
    }

  }

  public function getData($unsanitize = false) {

    global $config;

    return array(
      'id'          => $this->id,
      'title'       => ($unsanitize) ? unprepareText($this->title) : $this->title,
      'text'        => ($unsanitize) ? unprepareText($this->text) : $this->text,
      'subjectid'   => $this->subject->getData()['id'],
      'category'    => $this->category,
      'updatedate'  => $this->updatedate->format($config['dateformat']),
      'ordernumber' => $this->ordernumber,
      'live'        => $this->live
    );

  }

}
