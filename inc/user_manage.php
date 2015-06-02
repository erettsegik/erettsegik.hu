<?php

require_once 'classes/user.class.php';
require_once 'classes/logger.class.php';
require_once 'inc/functions.php';

$index_var['location'][] = array(
  'url' => '/user_manage/',
  'name' => 'Felhasználói oldal'
);

$index_var['title'] = 'Felhasználói oldal';

if (!isset($_SESSION['userid'])) {

  $mode = 'form';

  if (isset($_POST['submit'])) {

    $user = new user();

    if ($user->login($_POST['name'], $_POST['password'])) {

      $_SESSION['status'] = 'success';
      $_SESSION['message'] = 'Sikeres bejelentkezés!';

      $logger = new logger();

      $logger->log($user->getData()['name'] . ' logged in');

      header('Location: /user_manage/');
      die();

    }

    $saved = array('name' => $_POST['name']);

    $status = 'error';
    $message = 'Ezekkel az adatokkal nem sikerült bejelentkezni!';

  }

} else {

  if (isset($_GET['action']) && $_GET['action'] == 'logout') {

    unset($_SESSION['userid']);
    $_SESSION['status'] = 'success';
    $_SESSION['message'] = 'Sikeres kijelentkezés!';

    header('Location: /');
    die();

  } else {

     $mode = 'loggedin';

  }

  if (isset($_GET['action']) && $_GET['action'] == 'change_password') {

    $mode = 'change_password';

    if (isset($_POST['submit'])) {

      if (isNotEmpty($_POST['new_password'])) {

        $user = new user($_SESSION['userid']);
        $user->changePassword($_POST['old_password'], $_POST['new_password']);

        $_SESSION['status'] = 'success';
        $_SESSION['message'] = 'Sikeres jelszóváltoztatás!';

        header('Location: /user_manage/');
        die();

      }

      $status = 'error';
      $message = 'Nem sikerült a jelszóváltoztatás!';

    }

  }

}

$rendertarget = $_SESSION['mobile'] ? 'mobile/user_manage.html' : 'user_manage.html';

echo $twig->render(
  $rendertarget,
  array(
    'index_var' => $index_var,
    'message'   => $message,
    'mode'      => $mode,
    'saved'     => isset($saved) ? $saved : null,
    'status'    => $status
  )
);
