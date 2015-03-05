<?php

ob_start('ob_gzhandler');

session_start();

$dir_level = 0;

require_once 'inc/functions.php';
require_once 'classes/user.class.php';
require_once 'vendor/autoload.php';

Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);

use Aptoma\Twig\Extension\MarkdownExtension;
use Aptoma\Twig\Extension\MarkdownEngine;

$engine = new MarkdownEngine\PHPLeagueCommonMarkEngine();

$twig->addExtension(new MarkdownExtension($engine));

$index_var = array();

try {

  $getSubjects = $con->query('select id, name from subjects where mandatory = 1');

} catch (PDOException $e) {
  die($config['errors']['database']);
}

$index_var['subjects'] = array();

while ($subject = $getSubjects->fetch()) {

  $index_var['subjects'][$subject['id']] = $subject;

  $index_var['subjects'][$subject['id']]['categories'] = array();

  try {

    $getNotesData = $con->prepare('
      select notes.id, categories.name
      from notes
      left join categories on notes.category = categories.id
      where notes.subjectid = :subjectid and notes.live = 1
      order by category asc, ordernumber asc, id asc
    ');
    $getNotesData->bindValue(
      'subjectid',
      $subject['id'],
      PDO::PARAM_INT
    );
    $getNotesData->execute();

  } catch (PDOException $e) {
    die($config['errors']['database']);
  }

  while ($notesData = $getNotesData->fetch()) {

    if (!isset($index_var['subjects'][$subject['id']]['categories'][$notesData['name']]))
      $index_var['subjects'][$subject['id']]['categories'][$notesData['name']] = array('name' => $notesData['name']);

  }

}

try {

  $getSubjects = $con->query('select id, name from subjects where mandatory = 0');

} catch (PDOException $e) {
  die($config['errors']['database']);
}

$index_var['othersubjects'] = array();

while ($subject = $getSubjects->fetch()) {

  $index_var['othersubjects'][] = $subject;

}

$index_var['location'][] = array('url' => '/', 'name' => 'Főoldal');

if (isset($_SESSION['userid'])) {
  $user = new user($_SESSION['userid']);
  $username = $user->getData()['name'];
}

$index_var['username'] = (isset($_SESSION['userid'])) ? $username : '';

$index_var['css'] = '';

$index_var['canonical'] = getCanonicalUrl();

if (isset($_SESSION['status'])) {
  $status = $_SESSION['status'];
  $message = $_SESSION['message'];
  unset($_SESSION['status']);
} else {
  $status = 'none';
}

if (isset($_GET['p'])) {

  $p = $_GET['p'];

  if (file_exists('css/' . $p . '.css')) {

    $index_var['css'] = $p;

  }

  if (file_exists('inc/' . $p . '.php') && $p != 'functions') {

    require_once 'inc/' . $p . '.php';

  } else {

    header('Location: /404/');

  }

} else {

  $index_var['css'] = 'news';

  require_once 'inc/news.php';

}

function getCanonicalUrl() {

  $url = 'https://erettsegik.hu/';

  if (isset($_GET['p'])) {
    $url .= $_GET['p'] . '/';
  }

  if (isset($_GET['action'])) {
    $url .= $_GET['action'] . '/';
  }

  if (isset($_GET['id'])) {
    $url .= $_GET['id'] . '/';
  }

  return $url;

}

ob_end_flush();
