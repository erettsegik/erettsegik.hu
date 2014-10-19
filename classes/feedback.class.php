<?php

class feedback {

    protected $id    = null;
    protected $title = null;
    protected $text  = null;
    protected $date  = null;
    protected $isnew = null;

    public function __construct($id = null) {

        global $con;

        $selectData = $con->prepare('select * from feedback where id = :id');
        $selectData->bindValue('id', $id, PDO::PARAM_INT);
        $selectData->execute();

        $subjectData = $selectData->fetch();

        $this->id    = $subjectData['id'];
        $this->title = $subjectData['title'];
        $this->text  = $subjectData['text'];
        $this->date  = $subjectData['date'];
        $this->isnew = $subjectData['isnew'];

    }

    public function makeNotNew() {

        global $con;

        try {

            $modifyData = $con->prepare('
                update feedback
                set isnew = 0
                where id = :id
            ');
            $modifyData->bindValue('id', $this->id, PDO::PARAM_INT);
            $modifyData->execute();

        } catch (PDOException $e) {
            die('Nem sikerült a frissítés.');
        }

    }

    public function insertData($title, $text) {

        global $con;

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
            $insertData->bindValue('title', $title, PDO::PARAM_STR);
            $insertData->bindValue('text', $text, PDO::PARAM_STR);
            $insertData->execute();

        } catch (PDOException $e) {
            die('Nem sikerült a visszajelzés elküldése.');
        }

    }

    public function getData() {

        return array(
            'id'    => $this->id,
            'title' => $this->title,
            'text'  => $this->text,
            'date'  => $this->date,
            'isnew' => $this->isnew
        );

    }

}
