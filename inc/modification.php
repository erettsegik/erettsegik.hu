<?php

require_once 'classes/modification.class.php';
require_once 'classes/note.class.php';

if (isset($_GET['action']) && $_GET['action'] == 'add') {

    $index_var['title'] = 'Javaslat hozzáadása';

    $index_var['location'][] = array(
        'url' => '?p=modification',
        'name' => 'Javaslat hozzáadása'
    );

    $note = new note($_GET['noteid']);

    if (isset($_POST['submit'])) {

        $status = 'submit';

        if (isValid('note', $_GET['noteid'])) {

            $modification = new modification();

            $modification->insertData(
                $_GET['noteid'],
                $_POST['title'],
                $_POST['original_text'],
                $_POST['new_text'],
                $_POST['comment']
            );

        } else {

            die('Érvénytelen!');

        }

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

    $index_var['title'] = $modification->getData()['title'];

    $note = new note($modification->getData()['noteid']);

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
