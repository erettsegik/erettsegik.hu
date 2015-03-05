<?php

class feedback {

  protected $id    = null;
  protected $title = null;
  protected $text  = null;
  protected $date  = null;
  protected $isnew = null;

  public function __construct($id = null) {

    global $con;
    global $config;

    try {

      $selectData = $con->prepare('select * from feedback where id = :id');
      $selectData->bindValue('id', $id, PDO::PARAM_INT);
      $selectData->execute();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

    $feedbackData = $selectData->fetch();

    $this->id    = $feedbackData['id'];
    $this->title = $feedbackData['title'];
    $this->text  = $feedbackData['text'];
    $this->date  = new DateTime($feedbackData['date']);
    $this->isnew = $feedbackData['isnew'];

  }

  public function makeNotNew() {

    global $con;
    global $config;

    try {

      $modifyData = $con->prepare('
        update feedback
        set isnew = 0
        where id = :id
      ');
      $modifyData->bindValue('id', $this->id, PDO::PARAM_INT);
      $modifyData->execute();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

  }

  public function insertData($title, $text) {

    global $con;
    global $config;

    $this->title = $title;
    $this->text  = $text;

    try {

      $insertData = $con->prepare('
        insert into feedback
        values(
          DEFAULT,
          :title,
          :text,
          DEFAULT,
          1
        )
      ');
      $insertData->bindValue('title', $this->title, PDO::PARAM_STR);
      $insertData->bindValue('text', $this->text, PDO::PARAM_STR);
      $insertData->execute();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

  }

  public function getData() {

    global $config;

    return array(
      'id'    => $this->id,
      'title' => $this->title,
      'text'  => $this->text,
      'date'  => $this->date->format($config['dateformat']),
      'isnew' => $this->isnew
    );

  }

}
