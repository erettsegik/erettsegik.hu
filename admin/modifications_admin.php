<?php

require_once '../classes/modification.class.php';
require_once '../classes/note.class.php';

checkRights($config['clearance']['modifications']);

if (isset($_GET['id'])) {

  $status = 'one';

  $modification = new modification($_GET['id']);

  if (isset($_POST['submit'])) {

    $modification->updateStatus($_POST['status'], $_POST['reply']);

  }

  $modificationData = $modification->getData();
  $note = new note($modificationData['noteid']);
  $noteData = $note->getData();

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
    die($config['errors']['database']);
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
    'index_var'        => $index_var,
    'modificationdata' => isset($modificationData) ? $modificationData : null,
    'modifications'    => isset($modifications) ? $modifications : null,
    'notedata'         => isset($noteData) ? $noteData : null,
    'status'           => $status
  )
);
