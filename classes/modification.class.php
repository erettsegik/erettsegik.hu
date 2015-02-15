<?php

require_once 'note.class.php';

class modification {

  protected $id         = null;
  protected $note       = null;
  protected $title      = null;
  protected $start_text = null;
  protected $old_text   = null;
  protected $new_text   = null;
  protected $end_text   = null;
  protected $comment    = null;
  protected $date       = null;
  protected $updatedate = null;
  protected $status     = null;
  protected $reply      = null;

  public function __construct($id = null) {

    global $con;

    try {

      $selectData = $con->prepare('
        select *
        from modifications
        where id = :id
      ');
      $selectData->bindValue('id', $id, PDO::PARAM_INT);
      $selectData->execute();

      $noteData = $selectData->fetch();

    } catch (PDOException $e) {
      die('Nem sikerült a javaslat betöltése.');
    }

    $this->id         = $noteData['id'];
    $this->note       = new note($noteData['noteid']);
    $this->title      = $noteData['title'];
    $this->start_text = $noteData['start_text'];
    $this->old_text   = $noteData['old_text'];
    $this->new_text   = $noteData['new_text'];
    $this->end_text   = $noteData['end_text'];
    $this->comment    = $noteData['comment'];
    $this->date       = new DateTime($noteData['date']);
    $this->updatedate = $noteData['updatedate'];
    $this->status     = $noteData['status'];
    $this->reply      = $noteData['reply'];

  }

  public function insertData($noteid, $title, $modified, $comment) {

    global $con;

    $note = new note($noteid);

    $original = $note->getData()['text'];

    $this->note    = new note($noteid);
    $this->title   = $title;
    $this->comment = $comment;
    $this->date    = new DateTime();

    $i = 0;
    while ($original[$i] == $modified[$i]) {
      $i++;
    }

    $o_i = strlen($original) - 1;
    $n_i = strlen($modified) - 1;

    while ($original[$o_i] == $modified[$n_i]) {
      $o_i--;
      $n_i--;
    }

    $this->start_text = substr($modified, max(0, $i-200), min($i, 200));
    $this->old_text   = substr($original, $i, max(0, $o_i - $i + 1));
    $this->new_text   = substr($modified, $i, max(0, $n_i - $i + 1));
    $this->end_text   = substr($modified, $n_i + 1, 200);

    try {

      $insertData = $con->prepare('
        insert into modifications
        values(
          DEFAULT,
          :noteid,
          :title,
          :start_text,
          :old_text,
          :new_text,
          :end_text,
          :comment,
          DEFAULT,
          DEFAULT,
          0,
          ""
        )
      ');
      $insertData->bindValue('noteid', $noteid, PDO::PARAM_INT);
      $insertData->bindValue('title', $this->title, PDO::PARAM_STR);
      $insertData->bindValue('start_text', $this->start_text, PDO::PARAM_STR);
      $insertData->bindValue('old_text', $this->old_text, PDO::PARAM_STR);
      $insertData->bindValue('new_text', $this->new_text, PDO::PARAM_STR);
      $insertData->bindValue('end_text', $this->end_text, PDO::PARAM_STR);
      $insertData->bindValue('comment', $this->comment, PDO::PARAM_STR);
      $insertData->execute();

      $this->id = $con->lastInsertId();

    } catch (PDOException $e) {
      die('Nem sikerült elmenteni a javaslatot.');
    }

  }

  function updateStatus($status, $reply) {

    global $con;

    $this->status = $status;
    $this->reply = $reply;

    try {

      $updateStatus = $con->prepare('
        update modifications
        set status = :status,
          reply = :reply,
          updatedate = now()
        where id = :id
      ');
      $updateStatus->bindValue('status', $this->status, PDO::PARAM_INT);
      $updateStatus->bindValue('reply', $this->reply, PDO::PARAM_STR);
      $updateStatus->bindValue('id', $this->id, PDO::PARAM_INT);
      $updateStatus->execute();

    } catch (PDOException $e) {
      die('Nem sikerült a javaslatot frissíteni.');
    }

  }

  public function getData() {

    global $config;

    return array(
      'id'         => $this->id,
      'noteid'     => $this->note->getData()['id'],
      'title'      => $this->title,
      'start_text' => $this->start_text,
      'old_text'   => $this->old_text,
      'new_text'   => $this->new_text,
      'end_text'   => $this->end_text,
      'comment'    => $this->comment,
      'date'       => $this->date->format($config['dateformat']),
      'updatedate' => $this->updatedate,
      'status'     => $this->status,
      'reply'      => $this->reply
    );

  }

}
