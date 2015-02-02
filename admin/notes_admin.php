<?php

session_start();

$dir_level = 1;

require_once '../inc/functions.php';
require_once '../classes/category.class.php';
require_once '../classes/note.class.php';

if (!checkRights(2)) {
    header('Location: /?p=user_manage');
    die('Kerlek jelentkezz be.');
}

$twig = initTwig();

if (isset($_GET['action']) && $_GET['action'] == 'add') {

    if (isset($_POST['submit'])) {

        $status = 'submit';

        $note = new note();

        $live = (isset($_POST['live']) && $_POST['live'] == 'on');

        $note->insertData(
            prepareText($_POST['title']),
            prepareText($_POST['text']),
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

        if (isset($_POST['delete']) && $_POST['delete'] == 'on') {

            $note->remove();

        } else {

            $live = (isset($_POST['live']) && $_POST['live'] == 'on');

            $note->modifyData(
                prepareText($_POST['title']),
                prepareText($_POST['text']),
                $_POST['subjectid'],
                $_POST['category'],
                $live
            );

        }

    } else {

        $status = 'form';

        $note = new note($id);

        $noteData = $note->getData(true);

    }

} else {

    $status = 'list';

    $notes = array();

    try {

        $getNotes = $con->query('
            select notes.id, notes.title, subjects.name
            from notes
            left join subjects on notes.subjectid = subjects.id
            order by notes.category asc, notes.id asc
        ');

    } catch (PDOException $e) {
        die('Nem sikerült a jegyzetek kiválasztása.');
    }

    while ($noteData = $getNotes->fetch()) {

        if (!isset($subjects[$noteData['name']])) {

            $subjects[$noteData['name']] = array();

        }

        if (!isset($subjects[$noteData['name']]['data']))
            $subjects[$noteData['name']]['data'] = array();

        $subjects[$noteData['name']]['name'] = $noteData['name'];
        $subjects[$noteData['name']]['data'][] = array(
            'id' => $noteData['id'],
            'title' => $noteData['title']
        );

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
    'subjects' => (isset($subjects) ? $subjects : null),
    'subjectlist' => getSubjects(),
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
            order by mandatory desc, name asc
        ');

    } catch (PDOException $e) {
        die('Nem sikerült a tárgyak kiválasztása.');
    }

    while ($subject = $getSubjects->fetch()) {
        $subjects[] = $subject;
    }

    return $subjects;
}
