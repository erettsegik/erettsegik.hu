<?php

session_start();

$dir_level = 1;

require_once '../inc/functions.php';
require_once '../classes/subject.class.php';

if (!checkRights(2)) {
  header('Location: /user_manage/');
  die('Kérlek jelentkezz be.');
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

    $mandatory = isset($_POST[$id . 'mandatory']) && $_POST[$id . 'mandatory'] == 'on';

    $subject->modifyData($_POST[$id . 'name'], $mandatory);

  }

}

if (isset($_POST['addnew'])) {

  $subject = new subject();

  $mandatory = isset($_POST['mandatory']) && $_POST['mandatory'] == 'on';

  $subject->insertData($_POST['name'], $mandatory);

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
