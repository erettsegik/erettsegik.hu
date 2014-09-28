<?php

require_once 'classes/note.class.php';

$note = new note($_GET['id']);

echo $twig->render(
    'note.html',
    array(
        'subjects' => $subjects,
        'note' => $note->getData()
    )
);
