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
    global $config;

    if ($id == null)
      return;

    try {

      $selectData = $con->prepare('
        select *
        from modifications
        where id = :id
      ');
      $selectData->bindValue('id', $id, PDO::PARAM_INT);
      $selectData->execute();

      $modificationData = $selectData->fetch();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

    $this->id         = $modificationData['id'];
    $this->note       = new note($modificationData['noteid']);
    $this->title      = $modificationData['title'];
    $this->start_text = $modificationData['start_text'];
    $this->old_text   = $modificationData['old_text'];
    $this->new_text   = $modificationData['new_text'];
    $this->end_text   = $modificationData['end_text'];
    $this->comment    = $modificationData['comment'];
    $this->date       = new DateTime($modificationData['date'], $config['tz']['utc']);
    $this->updatedate = $modificationData['updatedate'];
    $this->status     = $modificationData['status'];
    $this->reply      = $modificationData['reply'];

    $this->date->setTimezone($config['tz']['local']);

  }

  public function insertData($noteid, $title, $modified, $comment) {

    global $con;
    global $config;

    $note = new note($noteid);

    $original = $note->getData()['text'];

    $this->note    = new note($noteid);
    $this->title   = $title;
    $this->comment = $comment;

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

    $this->start_text = mb_substr($modified, max(0, $i-200), min($i, 200), 'utf-8');
    $this->old_text   = mb_substr($original, $i, max(0, $o_i - $i + 1), 'utf-8');
    $this->new_text   = mb_substr($modified, $i, max(0, $n_i - $i + 1), 'utf-8');
    $this->end_text   = mb_substr($modified, $n_i + 1, 200, 'utf-8');

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
      die($config['errors']['database']);
    }

  }

  function updateStatus($status, $reply) {

    global $con;
    global $config;

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
      die($config['errors']['database']);
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
