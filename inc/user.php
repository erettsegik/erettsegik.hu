<?php

require_once 'classes/user.class.php';
require_once 'classes/logger.class.php';
require_once 'inc/functions.php';

$index_var['location'][] = array(
  'url' => '/user/dashboard/',
  'name' => 'Felhasználói oldal'
);

$index_var['title'] = 'Felhasználói oldal';

if (isset($_SESSION['userid'])) {

  if (isset($_GET['action'])) {

    switch ($_GET['action']) {
      case 'dashboard': $action = 'dashboard'; break;
      case 'logout': $action = 'logout'; break;
      case 'settings': $action = 'settings'; break;
      default: header('Location: /user/dashboard/'); die();
    }

  } else {

    header('Location: /user/dashboard/');
    die();

  }

  $user = new user($_SESSION['userid']);

} else {

  if (isset($_GET['action'])) {

    switch ($_GET['action']) {
      case 'forgotten': $action = 'forgotten'; break;
      case 'login': $action = 'login'; break;
      case 'register': $action = 'register'; break;
      default: header('Location: /user/login/'); die();
    }

  } else {

    header('Location: /user/login/');
    die();

  }

}

if ($action == 'dashboard') {

  $userdata = $user->getData();

}

if ($action == 'logout') {

  unset($_SESSION['userid']);
  setcookie('remember', '', time()-3600, '/', NULL, 0);
  $_SESSION['status'] = 'success';
  $_SESSION['message'] = 'Sikeres kijelentkezés!';

  header('Location: /');
  die();

}

if ($action == 'settings') {

  if (isset($_POST['submit'])) {

    if (isNotEmpty($_POST['new_password'])) {

      $user->changePassword($_POST['old_password'], $_POST['new_password']);

      $_SESSION['status'] = 'success';
      $_SESSION['message'] = 'Sikeres jelszóváltoztatás!';

      header('Location: /user/dashboard/');
      die();

    }

    $status = 'error';
    $message = 'Nem sikerült a jelszóváltoztatás!';

  }

}

if ($action == 'forgotten') {

}

if ($action == 'login') {

  if (isset($_POST['submit'])) {

    $user = new user();

    $remember = (isset($_POST['remember']) && $_POST['remember'] == 'on');

    if ($user->login($_POST['name'], $_POST['password'], $remember)) {

      $_SESSION['status'] = 'success';
      $_SESSION['message'] = 'Sikeres bejelentkezés!';

      $logger = new logger();

      $logger->log($user->getData()['name'] . ' logged in');

      header('Location: /user/dashboard/');
      die();

    }

    $saved = array('name' => $_POST['name']);

    $status = 'error';
    $message = 'Ezekkel az adatokkal nem sikerült bejelentkezni!';

  }

}

if ($action == 'register') {

  if (isset($_POST['submit'])) {

    $user = new user();

    if ($user->register($_POST['name'], $_POST['email'], 0, $_POST['password'])) {

      $_SESSION['status'] = 'success';
      $_SESSION['message'] = 'Sikeres regisztráció!';

      $logger = new logger();

      $logger->log($user->getData()['name'] . ' registered');

      header('Location: /user/dashboard/');
      die();

    }

    $saved = array('name' => $_POST['name'], 'email' => $_POST['email']);

    $status = 'error';
    $message = $_SESSION['message'];

  }

}

echo $twig->render(
  'user.html',
  array(
    'index_var' => $index_var,
    'message'   => $message,
    'action'    => $action,
    'saved'     => isset($saved) ? $saved : null,
    'status'    => $status,
    'userdata'  => isset($userdata) ? $userdata : null,
  )
);
