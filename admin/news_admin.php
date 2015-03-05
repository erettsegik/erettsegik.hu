<?php

session_start();

$dir_level = 1;

require_once '../inc/functions.php';
require_once '../classes/news.class.php';

checkRights($config['clearance']['news']);

$user = new user($_SESSION['userid']);

$twig = initTwig();

if (isset($_GET['action']) && $_GET['action'] == 'add') {

  if (isset($_POST['submit'])) {

    $live = (isset($_POST['live']) && $_POST['live'] == 'on');

    $new = new news();

    $new->insertData(
      prepareText($_POST['title']),
      prepareText($_POST['text']),
      $_SESSION['userid'],
      $live
    );

    header('Location: /admin/news_admin.php');

  }

} else if (isset($_GET['action']) && $_GET['action'] == 'edit') {

  $new = new news($_GET['id']);

  if (isset($_POST['submit'])) {

    $live = (isset($_POST['live']) && $_POST['live'] == 'on');

    $new->modifyData(
      prepareText($_POST['title']),
      prepareText($_POST['text']),
      $live
    );

  }

  $newsData = $new->getData(true);

} else {

  $status = 'list';

  $news = array();

  try {

    $getNews = $con->query('
      select id
      from news
      order by date desc
    ');

  } catch (PDOException $e) {
    die($config['errors']['database']);
  }

  while ($noteData = $getNews->fetch()) {
    $note = new news($noteData['id']);
    $news[] = $note->getData();
  }

}

echo $twig->render(
  'admin/news_admin.html',
  array(
    'action'    => isset($_GET['action']) ? $_GET['action'] : null,
    'index_var' => array(
      'menu'           => getAdminMenuItems(),
      'user_authority' => $user->getData()['authority']
    ),
    'news'      => isset($news) ? $news : null,
    'newsdata'  => isset($newsData) ? $newsData : null,
    'status'    => $status
  )
);
