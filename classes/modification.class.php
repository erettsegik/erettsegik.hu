<?php

require_once 'note.class.php';

class modification {

    protected $id            = null;
    protected $note          = null;
    protected $title         = null;
    protected $original_text = null;
    protected $new_text      = null;
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
        $this->original_text = $noteData['original_text'];
        $this->new_text      = $noteData['new_text'];
        $this->comment       = $noteData['comment'];
        $this->date          = $noteData['date'];
        $this->updatedate    = $noteData['updatedate'];
        $this->status        = $noteData['status'];
        $this->reply         = $noteData['reply'];

    }

    public function insertData($noteid, $title, $original_text, $new_text, $comment) {

        global $con;

        $this->note          = new note($noteid);
        $this->title         = $title;
        $this->original_text = $original_text;
        $this->new_text      = $new_text;
        $this->comment       = $comment;

        try {

            $insertData = $con->prepare('
                insert into modifications
                values(
                    DEFAULT,
                    :noteid,
                    :title,
                    :original_text,
                    :new_text,
                    :comment,
                    DEFAULT,
                    DEFAULT,
                    0,
                    ''
                )
            ');
            $insertData->bindValue('noteid', $noteid, PDO::PARAM_INT);
            $insertData->bindValue('title', $title, PDO::PARAM_STR);
            $insertData->bindValue('original_text', $original_text, PDO::PARAM_STR);
            $insertData->bindValue('new_text', $new_text, PDO::PARAM_STR);
            $insertData->bindValue('comment', $comment, PDO::PARAM_STR);
            $insertData->execute();

            $this->id = $con->lastInsertId();

        } catch (PDOException $e) {
            die('Nem sikerült elmenteni a javaslatot.');
        }

    }

    public function getData() {

        return array(
            'id'            => $this->id,
            'noteid'        => $this->note->getData()['id'],
            'title'         => $this->title,
            'original_text' => $this->original_text,
            'new_text'      => $this->new_text,
            'comment'       => $this->comment,
            'date'          => $this->date,
            'updatedate'    => $this->updatedate,
            'status'        => $this->status,
            'reply'         => $this->reply
        );

    }

}
