<?php

require_once 'classes/collection.class.php';
require_once 'classes/note.class.php';
require_once 'classes/subject.class.php';

$index_var['location'][] = array('url' => '/collection/', 'name' => 'Gyűjteményem');

$index_var['title'] = 'Gyűjteményem';

if (isset($_SESSION['userid'])) {

  $mode = 'list';

  $notes = array();

  try {

    $getCollection = $con->prepare('
      select id
      from collections
      where userid = :userid
      order by learned asc
    ');
    $getCollection->bindValue('userid', $_SESSION['userid'], PDO::PARAM_INT);
    $getCollection->execute();

  } catch (PDOException $e) {
    die($config['errors']['database']);
  }

  while ($collectionData = $getCollection->fetch()) {

    $collection = new collection($collectionData['id']);
    $note = new note($collection->getData()['noteid']);
    $tempData = $note->getData();
    $notes[] = array(
      'id' => $tempData['id'],
      'title' => $tempData['title'],
      'subjectid' => $tempData['subjectid'],
      'incomplete' => $tempData['incomplete'],
      'learned' => $collection->getData()['learned'],
    );

    $collection->getData();

  }

  $subjectids = array();

  $temp = array();

  foreach ($notes as $key => $row) {
    $temp[$key] = $row['subjectid'];
    $subjectids[] = $row['subjectid'];
  }

  array_unique($subjectids);

  $subjects = array();

  foreach ($subjectids as $id) {
    $subject = new subject($id);
    $subjects[$subject->getData()['id']] = $subject->getData()['name'];
  }

  array_multisort($temp, SORT_ASC, $notes);

} else {

  $mode = 'stranger';

}

echo $twig->render('collection.html', array(
  'index_var' => $index_var,
  'mode' => $mode,
  'notes' => isset($notes) ? $notes : null,
  'subjects' => isset($subjects) ? $subjects : null,
));
