<?php

session_start();

$dir_level = 1;

require_once '../inc/functions.php';
require_once '../classes/subject.class.php';

$twig = initTwig();

try {

    $getSubjects = $con->prepare('select id from subjects');

} catch (PDOException $e) {
    die('Nem sikerült a tárgyak kiválasztása.');
}

$getSubjects->execute();

if (isset($_POST['update'])) {

    while ($subjectData = $getSubjects->fetch()) {

        $id = $subjectData['id'];

        $subject = new subject($id);

        $subject->modifyData($_POST[$id . 'name'], $_POST[$id . 'category']);

    }

}

if (isset($_POST['addnew'])) {

    $subject = new subject();

    $subject->insertData($_POST['name'], $_POST['category']);

}

$getSubjects->execute();

$subjects = array();

while ($subjectData = $getSubjects->fetch()) {

    $subject = new subject($subjectData['id']);
    $subjects[] = $subject->getData();

}

echo $twig->render('admin/subjects_admin.html', array('subjects' => $subjects));
