<?php

require_once 'subject.class.php';

class note {

    protected $id      = null;
    protected $title   = null;
    protected $text    = null;
    protected $subject = null;
    protected $level   = null;

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

        $this->id      = $noteData['id'];
        $this->title   = $noteData['title'];
        $this->text    = $noteData['text'];
        $this->subject = new subject($noteData['subjectid']);
        $this->level   = $noteData['level'];

    }

    public function insertData($title, $text, $subjectid, $level) {

        global $con;

        $this->title   = $title;
        $this->text    = $text;
        $this->subject = new subject($subjectid);
        $this->level   = $level;

        try {

            $insertData = $con->prepare('
                insert into notes
                values(
                    DEFAULT,
                    :title,
                    :text,
                    :subjectid,
                    :level
                )
            ');
            $insertData->bindValue('title', $title, PDO::PARAM_STR);
            $insertData->bindValue('text', $text, PDO::PARAM_STR);
            $insertData->bindValue('subjectid', $subjectid, PDO::PARAM_INT);
            $insertData->bindValue('level', $level, PDO::PARAM_INT);
            $insertData->execute();

            $this->id = $con->lastInsertId();

        } catch (PDOException $e) {
            die('Nem sikerült elmenteni a jegyzetet.');
        }

    }

    public function modifyData($title, $text, $subjectid, $level) {

        global $con;

        $this->title   = $title;
        $this->text    = $text;
        $this->subject = new subject($subjectid);
        $this->level   = $level;

        try {

            $modifyData = $con->prepare('
                update notes
                set title = :title,
                    text = :text,
                    subjectid = :subjectid,
                    level = :level
                where id = :id
            ');
            $modifyData->bindValue('title', $title, PDO::PARAM_STR);
            $modifyData->bindValue('text', $text, PDO::PARAM_STR);
            $modifyData->bindValue('subjectid', $subjectid, PDO::PARAM_INT);
            $modifyData->bindValue('level', $level, PDO::PARAM_INT);
            $modifyData->bindValue('id', $this->id, PDO::PARAM_INT);
            $modifyData->execute();

        } catch (PDOException $e) {
            die('Nem sikerült a jegyzetet frissíteni.');
        }

    }

    public function getData() {

        return array(
            'id'        => $this->id,
            'title'     => $this->title,
            'text'      => $this->text,
            'subjectid' => $this->subject->getData()['id'],
            'level'     => $this->level
        );

    }

}
