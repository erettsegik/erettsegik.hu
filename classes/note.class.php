<?php

require_once 'subject.class.php';

class note {

    protected $id = null;
    protected $title = null;
    protected $text = null;
    protected $subject = null;
    protected $level = null;

    public function __construct($id = null) {

        global $con;

        $selectData = $con->prepare('select * from notes where id = :id');
        $selectData->bindValue('id', $id, PDO::PARAM_INT);
        $selectData->execute();

        $noteData = $selectData->fetch();

        $this->id      = $noteData['id'];
        $this->title   = $noteData['title'];
        $this->text    = $noteData['text'];
        $this->subject = new subject($noteData['subjectid']);
        $this->level   = $noteData['level'];

    }

    public function getData() {

        return array(
            'id' => $this->id,
            'title' => $this->title,
            'text' => $this->text,
            'subjectid' => $this->subject->getData()['id'],
            'level' => $this->level
        );

    }

}
