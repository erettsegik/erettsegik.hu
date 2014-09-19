<?php

class subject {

    protected $id = null;
    protected $name = null;
    protected $level = null;

    public function __construct($id = null) {

        global $con;

        $selectData = $con->prepare('select * from subjects where id = :id');
        $selectData->bindValue('id', $id, PDO::PARAM_INT);
        $selectData->execute();

        $subjectData = $selectData->fetch();

        $this->id    = $subjectData['id'];
        $this->name  = $subjectData['name'];
        $this->level = $subjectData['level'];

    }

    public function getData() {

        return array(
            'id' => $this->id,
            'name' => $this->name,
            'level' => $this->level
        );

    }

}
