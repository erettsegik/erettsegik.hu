<?php

require_once 'inc/functions.php';
require_once 'classes/user.class.php';

$index_var['location'][] = array(
  'url' => '/user_manage/',
  'name' => 'Felhasználói oldal'
);

$index_var['title'] = 'Felhasználói oldal';

if (!checkRights(0)) {

  $status = 'form';

  if (isset($_POST['submit'])) {

    $user = new user();
    $status = ($user->login($_POST['name'], $_POST['password']))
      ? 'success'
      : 'error';

    if ($status == 'success') {
      header('Location: /user_manage/');
    }

  }

} else {

  if (isset($_GET['action']) && $_GET['action'] == 'logout') {

    $status = 'logout';
    session_destroy();
    header('Location: /user_manage/');

  } else {

     $status = 'loggedin';

  }

  if (isset($_GET['action']) && $_GET['action'] == 'change_password') {

    $status = 'change_password';

    if (isset($_POST['submit'])) {

      if (isNotEmpty($_POST['new_password'])) {

        $user = new user($_SESSION['userid']);
        $user->changePassword($_POST['old_password'], $_POST['new_password']);

        header('Location: /user_manage/');

      } else {

        die('Érvénytelen!');

      }

    }

  }

}

echo $twig->render(
  'user_manage.html',
  array('index_var' => $index_var,'status' => $status)
);
