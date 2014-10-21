<?php

class news {

    protected $id         = null;
    protected $title      = null;
    protected $text       = null;
    protected $date       = null;
    protected $updatedate = null;
    protected $creatorid  = null;

    public function __construct($id = null) {

        global $con;

        try {

            $selectData = $con->prepare('select * from news where id = :id');
            $selectData->bindValue('id', $id, PDO::PARAM_INT);
            $selectData->execute();

            $newsData = $selectData->fetch();

        } catch (PDOException $e) {
            die('Nem sikerült a hír betöltése.');
        }

        $this->id         = $newsData['id'];
        $this->title      = $newsData['title'];
        $this->text       = $newsData['text'];
        $this->date       = $newsData['date'];
        $this->updatedate = $newsData['updatedate'];
        $this->creatorid  = $newsData['creatorid'];

    }

    public function insertData($title, $text) {

        global $con;

        $this->title = $title;
        $this->text  = $text;

        try {

            $insertData = $con->prepare('
                insert into news
                values(
                    DEFAULT,
                    :title,
                    :text,
                    DEFAULT,
                    DEFAULT,
                    0
                )
            ');
            $insertData->bindValue('title', $title, PDO::PARAM_STR);
            $insertData->bindValue('text', $text, PDO::PARAM_STR);
            $insertData->execute();

        } catch (PDOException $e) {
            die('Nem sikerült a hír hozzáadása.');
        }

    }

    public function modifyData($title, $text) {

        global $con;

        $this->title = $title;
        $this->text  = $text;

        try {

            $insertData = $con->prepare('
                update news
                set title = :title,
                    text = :text
                where id = :id
            ');
            $insertData->bindValue('title', $title, PDO::PARAM_STR);
            $insertData->bindValue('text', $text, PDO::PARAM_STR);
            $insertData->bindValue('id', $this->id, PDO::PARAM_INT);
            $insertData->execute();

        } catch (PDOException $e) {
            die('Nem sikerült a hír frissítése.');
        }

    }

    public function remove() {

        global $con;

        try {

            $removeData = $con->prepare('
                delete from news
                where id = :id
            ');
            $removeData->bindValue('id', $this->id, PDO::PARAM_INT);
            $removeData->execute();

        } catch (PDOException $e) {
            die('Nem sikerült a hír törlése.');
        }

    }

    public function getData() {

        return array(
             'id' => $this->id,
             'title' =>$this->title,
             'text' => $this->text,
             'date' => $this->date,
             'updatedate' => $this->updatedate,
             'creatorid' => $this->creatorid
        );

    }

}
