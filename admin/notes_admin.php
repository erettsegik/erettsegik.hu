<?php

session_start();

$dir_level = 1;

require_once '../inc/functions.php';
require_once '../classes/category.class.php';
require_once '../classes/note.class.php';

if (!checkRights(2)) {
    header('Location: admin_login.php');
    die('Kerlek jelentkezz be.');
}

$twig = initTwig();

if (isset($_GET['action']) && $_GET['action'] == 'add') {

    if (isset($_POST['submit'])) {

        $status = 'submit';

        $note = new note();

        $live = (isset($_POST['live']) && $_POST['live'] == 'on');

        $note->insertData(
            $_POST['title'],
            $_POST['text'],
            $_POST['subjectid'],
            $_POST['category'],
            $live
        );

    } else {

        $status = 'form';

    }

} else if (isset($_GET['action']) && $_GET['action'] == 'edit') {

    $id = $_GET['id'];

    if (isset($_POST['submit'])) {

        $status = 'submit';

        $note = new note($id);

        $live = (isset($_POST['live']) && $_POST['live'] == 'on');

        $note->modifyData(
            $_POST['title'],
            $_POST['text'],
            $_POST['subjectid'],
            $_POST['category'],
            $live
        );

    } else {

        $status = 'form';

        $note = new note($id);

        $noteData = $note->getData();

    }

} else {

    $status = 'list';

    $notes = array();

    try {

        $getNotes = $con->query('
            select id
            from notes
            order by subjectid asc
        ');

    } catch (PDOException $e) {
        die('Nem sikerült a jegyzetek kiválasztása.');
    }

    while ($noteData = $getNotes->fetch()) {
        $note = new note($noteData['id']);
        $notes[] = $note->getData();
    }

}

try {

    $getCategories = $con->query('select id from categories');

} catch (PDOException $e) {
    die('Nem sikerült a kategóriák kiválasztása.');
}

$categories = array();

while ($categoryData = $getCategories->fetch()) {

    $category = new category($categoryData['id']);
    $categories[] = $category->getData();

}

echo $twig->render('admin/notes_admin.html', array(
    'action' => (isset($_GET['action']) ? $_GET['action'] : null),
    'status' => $status,
    'notedata' => (isset($noteData)) ? $noteData : null,
    'notes' => (isset($notes) ? $notes : null),
    'subjects' => getSubjects(),
    'categories' => $categories
    )
);

function getSubjects() {

    global $con;

    $subjects = array();

    try {

        $getSubjects = $con->query('
            select id, name
            from subjects
            order by name asc
        ');

    } catch (PDOException $e) {
        die('Nem sikerült a tárgyak kiválasztása.');
    }

    while ($subject = $getSubjects->fetch()) {
        $subjects[] = $subject;
    }

    return $subjects;
}
