<?php

require_once 'subject.class.php';

class note {

    protected $id         = null;
    protected $title      = null;
    protected $text       = null;
    protected $subject    = null;
    protected $category   = null;
    protected $date       = null;
    protected $updatedate = null;

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

        $this->id         = $noteData['id'];
        $this->title      = $noteData['title'];
        $this->text       = $noteData['text'];
        $this->subjectid  = new subject($noteData['subjectid']);
        $this->category   = $noteData['category'];
        $this->date       = $noteData['date'];
        $this->updatedate = $noteData['updatedate'];

    }

    public function insertData($title, $text, $subjectid, $category) {

        global $con;

        $this->title    = $title;
        $this->text     = $text;
        $this->subject  = new subject($subjectid);
        $this->category = $category;

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
                    DEFAULT
                )
            ');
            $insertData->bindValue('title', $title, PDO::PARAM_STR);
            $insertData->bindValue('text', $text, PDO::PARAM_STR);
            $insertData->bindValue('subjectid', $subjectid, PDO::PARAM_INT);
            $insertData->bindValue('category', $category, PDO::PARAM_INT);
            $insertData->execute();

            $this->id = $con->lastInsertId();

        } catch (PDOException $e) {
            die('Nem sikerült elmenteni a jegyzetet.');
        }

    }

    public function modifyData($title, $text, $subjectid, $category) {

        global $con;

        $this->title    = $title;
        $this->text     = $text;
        $this->subject  = new subject($subjectid);
        $this->category = $category;

        try {

            $modifyData = $con->prepare('
                update notes
                set title = :title,
                    text = :text,
                    subjectid = :subjectid,
                    category = :category
                where id = :id
            ');
            $modifyData->bindValue('title', $title, PDO::PARAM_STR);
            $modifyData->bindValue('text', $text, PDO::PARAM_STR);
            $modifyData->bindValue('subjectid', $subjectid, PDO::PARAM_INT);
            $modifyData->bindValue('category', $category, PDO::PARAM_INT);
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
            'category'  => $this->category
        );

    }

}
