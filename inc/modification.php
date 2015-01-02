<?php

require_once 'classes/modification.class.php';
require_once 'classes/note.class.php';

if (isset($_GET['action']) && $_GET['action'] == 'add') {

    $index_var['location'][] = array(
        'url' => '?p=modification',
        'name' => 'Javaslat hozzáadása'
    );

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

    $index_var['location'][] = array(
        'url' => '?p=modification',
        'name' => 'Javaslat: ' . $modification->getData()['title']
    );

    $note = new note($modification->getData()['noteid']);

    $diff = formattedDiff(
        $modification->getData()['original_text'],
        $modification->getData()['new_text']
    );

}

echo $twig->render(
    'modification.html',
    array(
        'index_var' => $index_var,
        'modification' => isset($modification) ? $modification->getData() : null,
        'action' => isset($_GET['action']) ? $_GET['action'] : null,
        'note' => $note->getData(),
        'status' => $status,
        'diff' => isset($diff) ? $diff : null
    )
);
