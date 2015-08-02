<?php

require_once 'classes/collection.class.php';
require_once 'classes/note.class.php';

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
    $notes[] = array(
      'id' => $note->getData()['id'],
      'title' => $note->getData()['title'],
      'learned' => $collection->getData()['learned'],
    );

    $collection->getData();

  }

} else {

  $mode = 'stranger';

}

echo $twig->render('collection.html', array(
  'index_var' => $index_var,
  'mode' => $mode,
  'notes' => isset($notes) ? $notes : null,
));
