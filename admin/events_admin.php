<?php

session_start();

$dir_level = 1;

require_once '../inc/functions.php';
require_once '../classes/feedback.class.php';

checkRights($config['clearance']['events']);

$user = new user($_SESSION['userid']);

$twig = initTwig();


if (isset($_GET['action']) && $_GET['action'] == 'add') {

  if (isset($_POST['submit'])) {

    $status = 'submit';

  } else {

    $status = 'form';

  }

} else if (isset($_GET['action']) && $_GET['action'] == 'edit') {

  $id = $_GET['id'];

  if (isset($_POST['submit'])) {

    $status = 'submit';

  } else {

    $status = 'form';

  }

} else {

  $status = 'list';

}

echo $twig->render(
  'admin/events_admin.html',
  array(
    'status' => $status,
    'index_var' => array('menu' => getAdminMenuItems(), 'user_authority' => $user->getData()['authority'])
  )
);
