<?php

session_start();

$dir_level = 1;

require_once '../inc/functions.php';

checkRights(1);

$twig = initTwig();

$user = new user($_SESSION['userid']);

if (isset($_SESSION['status'])) {

  $status = $_SESSION['status'];
  $message = $_SESSION['message'];
  unset($_SESSION['status']);

} else {

  $status = 'none';

}

echo $twig->render(
  'admin/index.html',
  array(
    'index_var' => array(
      'menu'           => getAdminMenuItems(),
      'user_authority' => $user->getData()['authority']
    )
  )
);
