<?php

session_start();

$dir_level = 1;

require_once '../inc/functions.php';
require_once '../classes/modification.class.php';
require_once '../classes/note.class.php';

if (!checkRights(2)) {
    header('Location: /?p=login');
    die('Kerlek jelentkezz be.');
}

$twig = initTwig();

if (isset($_GET['id'])) {

    $status = 'one';

    $modification = new modification($_GET['id']);
    $modificationData = $modification->getData();

    if (isset($_GET['action'])) {

        if ($_GET['action'] == 'merge') {

            $note = new note($modification->getData()['noteid']);
            $note->modifyData(
                $note->getData()['title'],
                $modification->getData()['new_text'],
                $note->getData()['subjectid'],
                $note->getData()['category'],
                $note->getData()['live']
            );

            $modification->updateStatus(1);

            $status = 'merged';

        }

        if ($_GET['action'] == 'reject') {

            $modification->updateStatus(2);

            $status = 'rejected';

        }

    }

} else {

    $status = 'all';

    try {

        $getModifications = $con->prepare('
            select id
            from modifications
            order by id desc
        ');
        $getModifications->execute();

    } catch (PDOException $e) {
        die('Nem sikerült a javaslatok kiválasztása.');
    }

    $modifications = array();

    while ($modificationData = $getModifications->fetch()) {

        $modification = new modification($modificationData['id']);
        $modifications[] = $modification->getData();

    }

}

echo $twig->render(
    'admin/modifications_admin.html',
    array(
        'modificationdata' => (isset($modificationData)) ? $modificationData : null,
        'modifications' => (isset($modifications)) ? $modifications : null,
        'status' => $status
    )
);
