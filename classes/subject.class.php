<?php

class subject {

    protected $id       = null;
    protected $name     = null;
    protected $category = null;

    public function __construct($id = null) {

        global $con;

        $selectData = $con->prepare('select * from subjects where id = :id');
        $selectData->bindValue('id', $id, PDO::PARAM_INT);
        $selectData->execute();

        $subjectData = $selectData->fetch();

        $this->id       = $subjectData['id'];
        $this->name     = $subjectData['name'];
        $this->category = $subjectData['category'];

    }

    public function insertData($name, $category) {

        global $con;

        $this->name     = $name;
        $this->category = $category;

        try {

            $insertData = $con->prepare('
                insert into subjects
                values(
                    DEFAULT,
                    :name,
                    :category
                )
            ');
            $insertData->bindValue('name', $name, PDO::PARAM_STR);
            $insertData->bindValue('category', $category, PDO::PARAM_INT);
            $insertData->execute();

        } catch (PDOException $e) {
            die('Nem sikerült a tárgy hozzáadása.');
        }

    }

    public function modifyData($name, $category) {

        global $con;

        if ($name == '') {

            $this->remove();

        } else {

            $this->name     = $name;
            $this->category = $category;

            try {

                $insertData = $con->prepare('
                    update subjects
                    set name = :name,
                        category = :category
                    where id = :id
                ');
                $insertData->bindValue('name', $name, PDO::PARAM_STR);
                $insertData->bindValue('category', $category, PDO::PARAM_INT);
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
            'id'       => $this->id,
            'name'     => $this->name,
            'category' => $this->category
        );

    }

}
