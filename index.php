<?php

ob_start('ob_gzhandler');

session_start();

$dir_level = 0;

require_once 'inc/functions.php';
require_once 'classes/user.class.php';
require_once 'vendor/autoload.php';

Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem('templates');

$filters = Array(
  new Twig_SimpleFilter('prepareLatexElements', 'prepareLatexElements'),
);

$twig = new Twig_Environment($loader);

foreach ($filters as $filter) {
  $twig->addFilter($filter);
}

use Aptoma\Twig\Extension\MarkdownExtension;
use Aptoma\Twig\Extension\MarkdownEngine;

$engine = new MarkdownEngine\GitHubMarkdownEngine('aptoma/twig-markdown', true, '/tmp/markdown-cache');
$twig->addExtension(new MarkdownExtension($engine));

$index_var = array();

if (isset($_COOKIE['remember']) && !isset($_SESSION['userid'])) {

  $array = json_decode($_COOKIE['remember'], true);

  try {

    $get = $con->prepare('select * from logins where session = :session');
    $get->bindValue('session', $array['session'], PDO::PARAM_STR);
    $get->execute();

  } catch (PDOException $e) {
    die($config['errors']['database']);
  }

  while ($session = $get->fetch()) {
    if ($session['hash'] === hash('sha256', $array['token'])) {
      $_SESSION['userid'] = $session['userid'];
      $user = new user($_SESSION['userid']);
    }
  }

}

try {

  $getSubjects = $con->query('select id, name from subjects where mandatory = 1 order by name asc');

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
      inner join categories on notes.category = categories.id
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

  $getSubjects = $con->query('select id, name from subjects where mandatory = 0 order by name asc');

} catch (PDOException $e) {
  die($config['errors']['database']);
}

$index_var['othersubjects'] = array();

while ($subject = $getSubjects->fetch()) {

  $index_var['othersubjects'][] = $subject;

}

$index_var['location'] = array();

if (isset($_SESSION['userid'])) {
  $user = new user($_SESSION['userid']);
  $username = $user->getData()['name'];
}

$index_var['username'] = (isset($_SESSION['userid'])) ? $username : '';

$index_var['css'] = '';
$index_var['mobilecss'] = '';

$index_var['canonical'] = getCanonicalUrl();

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

  if (file_exists('inc/' . $p . '.php') && $p != 'functions') {

    require_once 'inc/' . $p . '.php';

  } else {

    header('Location: /404/');
    die();

  }

} else {

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
