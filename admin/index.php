<?php

ob_start('ob_gzhandler');

session_start();

$dir_level = 1;

require_once '../inc/functions.php';

checkRights(1);

$twig = initTwig();

$filters = Array(
    new Twig_SimpleFilter('prepareLatexElements', 'prepareLatexElements'),
);

foreach ($filters as $filter) {
    $twig->addFilter($filter);
}

use Aptoma\Twig\Extension\MarkdownExtension;
use Aptoma\Twig\Extension\MarkdownEngine;

$engine = new MarkdownEngine\GitHubMarkdownEngine('aptoma/twig-markdown', true, '/tmp/markdown-cache');

$twig->addExtension(new MarkdownExtension($engine));

$adminMenuItems = array(
  array('url' => 'events_admin', 'name' => 'Események', 'clearance' => $config['clearance']['events']),
  array('url' => 'users_admin', 'name' => 'Felhasználók', 'clearance' => $config['clearance']['users']),
  array('url' => 'news_admin', 'name' => 'Hírek', 'clearance' => $config['clearance']['news']),
  array('url' => 'modifications_admin', 'name' => 'Javaslatok', 'clearance' => $config['clearance']['modifications']),
  array('url' => 'notes_admin', 'name' => 'Jegyzetek', 'clearance' => $config['clearance']['notes']),
  array('url' => 'categories_admin', 'name' => 'Kategóriák', 'clearance' => $config['clearance']['categories']),
  array('url' => 'subjects_admin', 'name' => 'Tárgyak', 'clearance' => $config['clearance']['subjects']),
  array('url' => 'feedback_admin', 'name' => 'Visszajelzések', 'clearance' => $config['clearance']['feedback']),
  array('url' => 'logs_admin', 'name' => 'Logok', 'clearance' => $config['clearance']['logs']),
);

$user = new user($_SESSION['userid']);

$index_var = array(
  'menu' => $adminMenuItems,
  'user_authority' => $user->getData()['authority']
);

if (isset($_SESSION['status'])) {

  $status = $_SESSION['status'];
  $message = $_SESSION['message'];
  unset($_SESSION['status']);
  unset($_SESSION['message']);

} else {

  $status = 'none';
  $message = '';

}

if (isset($_GET['p'])) {

  $p = $_GET['p'];

  if (file_exists($p . '.php')) {

    require_once $p . '.php';

  } else {

    header('Location: /404/');

  }

} else {

  require_once 'notes_admin.php';

}

ob_end_flush();
