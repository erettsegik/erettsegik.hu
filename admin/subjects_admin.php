<?php

require_once '../classes/subject.class.php';

checkRights($config['clearance']['subjects']);

try {

  $getSubjects = $con->prepare('select id from subjects');

} catch (PDOException $e) {
  die($config['errors']['database']);
}

$getSubjects->execute();

if (isset($_POST['update'])) {

  while ($subjectData = $getSubjects->fetch()) {

    $id = $subjectData['id'];

    $subject = new subject($id);

    $mandatory = isset($_POST[$id . 'mandatory']) && $_POST[$id . 'mandatory'] == 'on';

    $subject->modifyData($_POST[$id . 'name'], $mandatory);

    $status = 'success';
    $message = 'Sikeres mentés!';

  }

}

if (isset($_POST['addnew'])) {

  $subject = new subject();

  $mandatory = isset($_POST['mandatory']) && $_POST['mandatory'] == 'on';

  $subject->insertData($_POST['name'], $mandatory);

  $status = 'success';
  $message = 'Sikeres mentés!';

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
    'index_var' => $index_var,
    'subjects'  => $subjects,
    'message'   => isset($message) ? $message : null,
    'status'    => $status
  )
);
