<?php

session_start();

$dir_level = 1;

require_once '../inc/functions.php';
require_once '../classes/note.class.php';

$twig = initTwig();

if (isset($_GET['action']) && $_GET['action'] == 'add') {

    if (isset($_POST['submit'])) {

        $status = 'submit';

        $note = new note();

        $note->insertData($_POST['title'], $_POST['text'], $_POST['subjectid'], $_POST['level']);

    } else {

        $status = 'form';

        $subjects = array();
        $getSubjects = $con->query('select id, name from subjects order by name asc');

        while ($subject = $getSubjects->fetch()) {
            $subjects[] = $subject;
        }

    }

} else if (isset($_GET['action']) && $_GET['action'] == 'edit') {

} else {

}

echo $twig->render('admin/notes_admin.html', array(
    'status' => $status,
    'subjects' => (isset($subjects)) ? $subjects : null
    )
);
