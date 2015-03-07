<?php

require_once 'classes/note.class.php';

class mocknote extends note
{
    public function __construct($id = null)
    {
        return;
    }

    public function insertData($text)
    {
        $this->text = $text;
    }
}

$note = new mocknote();
$note->insertData(prepareText($_POST['text']));

echo $twig->render('note_text.html', array('note' => $note->getData()));
