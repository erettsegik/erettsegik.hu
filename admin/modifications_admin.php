<?php

session_start();

$dir_level = 1;

require_once '../inc/functions.php';
require_once '../classes/modification.class.php';
require_once '../classes/note.class.php';

checkRights($config['clearance']['modifications']);

$user = new user($_SESSION['userid']);

$twig = initTwig();

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
    die('Nem sikerült a javaslatok kiválasztása.');
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
    'modificationdata' => (isset($modificationData)) ? $modificationData : null,
    'modifications' => (isset($modifications)) ? $modifications : null,
    'notedata' => (isset($noteData)) ? $noteData : null,
    'status' => $status,
    'index_var' => array('menu' => getAdminMenuItems(), 'user_authority' => $user->getData()['authority'])
  )
);
