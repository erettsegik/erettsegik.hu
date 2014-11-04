<?php

require_once 'classes/modification.class.php';
require_once 'classes/note.class.php';

if (isset($_GET['action']) && $_GET['action'] == 'add') {

    if (isset($_POST['submit'])) {

    } else {

    }

    die('Soon.');

} else {

    $note = new note($_GET['id']);

    $getModificationsData = $con->prepare('
        select id
        from modifications
        where noteid = :noteid
    ');
    $getModificationsData->bindValue('noteid', $note->getData()['id'], PDO::PARAM_INT);
    $getModificationsData->execute();

    $modifications = array();

    while ($modificationData = $getModificationsData->fetch()) {
        $modification = new modification($modificationData['id']);
        $modifications[] = $modification->getData();
    }

}

echo $twig->render(
    'note.html',
    array(
        'subjects' => $subjects,
        'note' => $note->getData(),
        'modifications' => $modifications
    )
);
