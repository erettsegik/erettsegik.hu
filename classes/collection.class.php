<?php

class collection {

  protected $id   = null;
  protected $userid = null;
  protected $noteid = null;
  protected $learned = null;

  public function __construct($id = null) {

    global $con;
    global $config;

    if ($id == null)
      return;

    try {

      $selectData = $con->prepare('select * from collections where id = :id');
      $selectData->bindValue('id', $id, PDO::PARAM_INT);
      $selectData->execute();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

    $categoryData = $selectData->fetch();

    $this->id   = $categoryData['id'];
    $this->userid   = $categoryData['userid'];
    $this->noteid   = $categoryData['noteid'];
    $this->learned   = $categoryData['learned'];

  }

  public function insertData($userid, $noteid, $learned) {

    global $con;
    global $config;

    $this->userid = $userid;
    $this->noteid = $noteid;
    $this->learned = $learned;

    try {

      $insertData = $con->prepare('
        insert into collections
        values(
          DEFAULT,
          :userid,
          :noteid,
          :learned
        )
      ');
      $insertData->bindValue('userid', $this->userid, PDO::PARAM_INT);
      $insertData->bindValue('noteid', $this->noteid, PDO::PARAM_INT);
      $insertData->bindValue('learned', $this->learned, PDO::PARAM_INT);
      $insertData->execute();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

  }

  public function modifyData($name) {

    global $con;
    global $config;

    if ($name == '') {

      $this->remove();

    } else {

      $this->userid = $userid;
      $this->noteid = $noteid;
      $this->learned = $learned;

      try {

        $insertData = $con->prepare('
          insert into collections
          values(
            DEFAULT,
            :userid,
            :noteid,
            :learned
          )
        ');
        $insertData = $con->prepare('
          update collections
          set userid = :userid, noteid = :noteid, learned = :learned
          where id = :id
        ');
        $insertData->bindValue('userid', $this->userid, PDO::PARAM_INT);
        $insertData->bindValue('noteid', $this->noteid, PDO::PARAM_INT);
        $insertData->bindValue('learned', $this->learned, PDO::PARAM_INT);
        $insertData->execute();

      } catch (PDOException $e) {
        die($config['errors']['database']);
      }

    }

  }

  public function remove() {

    global $con;
    global $config;

    try {

      $removeData = $con->prepare('
        delete from collections
        where id = :id
      ');
      $removeData->bindValue('id', $this->id, PDO::PARAM_INT);
      $removeData->execute();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

  }

  public function getData() {

    return array(
      'id'   => $this->id,
      'userid' => $this->userid,
      'noteid' => $this->noteid,
      'learned' => $this->learned,
    );

  }

}
