<?php

require_once 'note.class.php';

class modification {

    protected $id            = null;
    protected $note          = null;
    protected $title         = null;
    protected $start_text    = null;
    protected $difference    = null;
    protected $end_text      = null;
    protected $comment       = null;
    protected $date          = null;
    protected $updatedate    = null;
    protected $status        = null;
    protected $reply         = null;

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

        $this->id            = $noteData['id'];
        $this->note          = new note($noteData['noteid']);
        $this->title         = $noteData['title'];
        $this->start_text    = $noteData['start_text'];
        $this->difference    = $noteData['difference'];
        $this->end_text      = $noteData['end_text'];
        $this->comment       = $noteData['comment'];
        $this->date          = new DateTime($noteData['date']);
        $this->updatedate    = $noteData['updatedate'];
        $this->status        = $noteData['status'];
        $this->reply         = $noteData['reply'];

    }

    public function insertData($noteid, $title, $original_text, $new_text, $comment) {

        global $con;

        $this->note    = new note($noteid);
        $this->title   = $title;
        $this->comment = $comment;
        $this->date    = new DateTime();

        $i = 0;
        while ($original_text[$i] == $new_text[$i]) {
            $i++;
        }

        $o_i = strlen($original_text);
        $n_i = strlen($new_text);

        while ($original_text[$o_i] == $new_text[$n_i]) {
            $o_i--;
            $n_i--;
        }

        $this->start_text = substr($new_text, max(0, $i-200), min($i, 200));
        $this->difference = substr($new_text, $i, $n_i - $i + 1);
        $this->end_text   = substr($new_text, $n_i + 1, min(strlen($new_text) - $n_i, 200));

        try {

            $insertData = $con->prepare('
                insert into modifications
                values(
                    DEFAULT,
                    :noteid,
                    :title,
                    :start_text,
                    :difference,
                    :end_text,
                    :comment,
                    DEFAULT,
                    DEFAULT,
                    0,
                    ""
                )
            ');
            $insertData->bindValue('noteid', $noteid, PDO::PARAM_INT);
            $insertData->bindValue('title', $title, PDO::PARAM_STR);
            $insertData->bindValue('start_text', $this->start_text, PDO::PARAM_STR);
            $insertData->bindValue('difference', $this->difference, PDO::PARAM_STR);
            $insertData->bindValue('end_text', $this->end_text, PDO::PARAM_STR);
            $insertData->bindValue('comment', $comment, PDO::PARAM_STR);
            $insertData->execute();

            $this->id = $con->lastInsertId();

        } catch (PDOException $e) {
            die('Nem sikerült elmenteni a javaslatot.');
        }

    }

    function updateStatus($status, $reply = null) {

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
            $updateStatus->bindValue('status', $status, PDO::PARAM_INT);
            $updateStatus->bindValue('reply', $reply, PDO::PARAM_STR);
            $updateStatus->bindValue('id', $this->id, PDO::PARAM_INT);
            $updateStatus->execute();

        } catch (PDOException $e) {
            die('Nem sikerült a javaslatot frissíteni.');
        }

    }

    public function getData() {

        global $config;

        return array(
            'id'            => $this->id,
            'noteid'        => $this->note->getData()['id'],
            'title'         => $this->title,
            'start_text'    => $this->start_text,
            'difference'    => $this->difference,
            'end_text'      => $this->end_text,
            'comment'       => $this->comment,
            'date'          => $this->date->format($config['dateformat']),
            'updatedate'    => $this->updatedate,
            'status'        => $this->status,
            'reply'         => $this->reply
        );

    }

}
