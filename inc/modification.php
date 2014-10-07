<?php

require_once 'classes/modification.class.php';
require_once 'classes/note.class.php';

if (isset($_GET['action']) && $_GET['action'] == 'add') {

    $note = new note($_GET['noteid']);

    if (isset($_POST['submit'])) {

        $status = 'submit';

        $modification = new modification();

        $modification->insertData(
            $_GET['noteid'],
            $_POST['title'],
            $_POST['original_text'],
            $_POST['new_text'],
            $_POST['comment']
        );

    } else {

        $status = 'form';

    }

} else {

    $status = 'display';

    $modification = new modification($_GET['id']);
    $note = new note($modification->getData()['noteid']);

}

echo $twig->render(
    'modification.html',
    array(
        'modification' => isset($modification) ? $modification->getData() : null,
        'subjects' => $subjects,
        'action' => isset($_GET['action']) ? $_GET['action'] : null,
        'note' => $note->getData(),
        'status' => $status
    )
);
