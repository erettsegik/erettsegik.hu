<?php

session_start();

$dir_level = 1;

require_once '../inc/functions.php';
require_once '../classes/news.class.php';

if (!checkRights(2)) {
  header('Location: /user_manage/');
  die('Kérlek jelentkezz be.');
}

$twig = initTwig();

if (isset($_GET['action']) && $_GET['action'] == 'add') {

  if (isset($_POST['submit'])) {

    $status = 'submit';

    $live = (isset($_POST['live']) && $_POST['live'] == 'on');

    $new = new news();

    $new->insertData(
      prepareText($_POST['title']),
      prepareText($_POST['text']),
      $_SESSION['userid'],
      $live
    );

  } else {

    $status = 'form';

  }

} else if (isset($_GET['action']) && $_GET['action'] == 'edit') {

  $id = $_GET['id'];

  if (isset($_POST['submit'])) {

    $status = 'submit';

    $live = (isset($_POST['live']) && $_POST['live'] == 'on');

    $new = new news($id);

    $new->modifyData(
      prepareText($_POST['title']),
      prepareText($_POST['text']),
      $live
    );

  } else {

    $status = 'form';

    $new = new news($id);

    $newsData = $new->getData(true);

  }

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
    die('Nem sikerült a hírek kiválasztása.');
  }

  while ($noteData = $getNews->fetch()) {
    $note = new news($noteData['id']);
    $news[] = $note->getData();
  }

}

echo $twig->render('admin/news_admin.html', array(
  'action' => (isset($_GET['action']) ? $_GET['action'] : null),
  'status' => $status,
  'newsdata' => (isset($newsData)) ? $newsData : null,
  'news' => (isset($news) ? $news : null)
  )
);
