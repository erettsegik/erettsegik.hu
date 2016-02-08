<?php

require_once 'classes/user.class.php';
require_once 'classes/logger.class.php';
require_once 'inc/functions.php';

$index_var['location'][] = array(
  'url' => '/user/dashboard/',
  'name' => 'Profil'
);

$index_var['title'] = 'Profil';

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
  $userdata = $user->getData();

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

if ($action == 'logout') {

  $user->logout();
  $_SESSION['status'] = 'success';
  $_SESSION['message'] = 'Sikeres kijelentkezés!';

  header('Location: /');
  die();

}

if ($action == 'settings') {

  $index_var['location'][] = array(
    'url' => '/user/settings/',
    'name' => 'Beállítások'
  );

  if (isset($_POST['submit'])) {

    $user->modifyData($userdata['name'], $_POST['email'], $userdata['authority']);

    $_SESSION['status'] = 'success';
    $_SESSION['message'] = 'E-mail cím frissítve!';

    if (isNotEmpty($_POST['new_password'])) {

      $user->changePassword($_POST['old_password'], $_POST['new_password']);

      $_SESSION['status'] = 'success';
      $_SESSION['message'] = 'Sikeres e-mail és jelszóváltoztatás!';

    }

    header('Location: /user/dashboard/');
    die();

  }

}

if ($action == 'forgotten') {

}

if ($action == 'login') {

  $index_var['location'][] = array(
    'url' => '/user/login/',
    'name' => 'Bejelentkezés'
  );

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

  $index_var['location'][] = array(
    'url' => '/user/register/',
    'name' => 'Regisztráció'
  );

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
