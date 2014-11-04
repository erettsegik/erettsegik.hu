<?php

session_start();

$dir_level = 1;

require_once '../inc/functions.php';
require_once '../classes/subject.class.php';

if (!checkRights(2)) {
    header('Location: admin_login.php');
    die('Kerlek jelentkezz be.');
}

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

        $subject->modifyData($_POST[$id . 'name'], $_POST[$id . 'category'], $_POST[$id . 'mandatory']);

    }

}

if (isset($_POST['addnew'])) {

    $subject = new subject();

    $subject->insertData($_POST['name'], $_POST['category'], $_POST['mandatory']);

}

$getSubjects->execute();

$subjects = array();

while ($subjectData = $getSubjects->fetch()) {

    $subject = new subject($subjectData['id']);
    $subjects[] = $subject->getData();

}

echo $twig->render(
    'admin/subjects_admin.html',
    array(
        'subjects' => $subjects
    )
);
