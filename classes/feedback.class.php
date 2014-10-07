<?php

class feedback {

    protected $id   = null;
    protected $text = null;
    protected $date = null;

    public function __construct($id = null) {

        global $con;

        $selectData = $con->prepare('select * from feedback where id = :id');
        $selectData->bindValue('id', $id, PDO::PARAM_INT);
        $selectData->execute();

        $subjectData = $selectData->fetch();

        $this->id   = $subjectData['id'];
        $this->text = $subjectData['text'];
        $this->date = $subjectData['date'];

    }

    public function insertData($text) {

        global $con;

        $this->text  = $text;

        try {

            $insertData = $con->prepare('
                insert into subjects
                values(
                    DEFAULT,
                    :name,
                    :level
                )
            ');
            $insertData->bindValue('name', $name, PDO::PARAM_STR);
            $insertData->bindValue('level', $level, PDO::PARAM_INT);
            $insertData->execute();

        } catch (PDOException $e) {
            die('Nem sikerült a tárgy hozzáadása.');
        }

    }

    public function modifyData($name, $level) {

        global $con;

        if ($name == '') {

            $this->remove();

        } else {

            $this->name  = $name;
            $this->level = $level;

            try {

                $insertData = $con->prepare('
                    update subjects
                    set name = :name,
                        level = :level
                    where id = :id
                ');
                $insertData->bindValue('name', $name, PDO::PARAM_STR);
                $insertData->bindValue('level', $level, PDO::PARAM_INT);
                $insertData->bindValue('id', $this->id, PDO::PARAM_INT);
                $insertData->execute();

            } catch (PDOException $e) {
                die('Nem sikerült a tárgy frissítése.');
            }

        }

    }

    public function remove() {

        global $con;

        try {

            $removeSubject = $con->prepare('
                delete from subjects
                where id = :id
            ');
            $removeSubject->bindValue('id', $this->id, PDO::PARAM_INT);
            $removeSubject->execute();

        } catch (PDOException $e) {
            die('Nem sikerült a tárgy törlése.');
        }

    }

    public function getData() {

        return array(
            'id' => $this->id,
            'name' => $this->name,
            'level' => $this->level
        );

    }

}
