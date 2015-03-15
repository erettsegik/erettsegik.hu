<?php

require_once '../classes/news.class.php';

checkRights($config['clearance']['news']);

if (isset($_GET['action']) && $_GET['action'] == 'add') {

  if (isset($_POST['submit'])) {

    $live = (isset($_POST['live']) && $_POST['live'] == 'on');

    $new = new news();

    $new->insertData(
      $_POST['title'],
      prepareText($_POST['text']),
      $_SESSION['userid'],
      $live
    );

    $_SESSION['status'] = 'success';
    $_SESSION['message'] = 'Sikeres mentés!';

    header('Location: index.php?p=news_admin');

  }

} else if (isset($_GET['action']) && $_GET['action'] == 'edit') {

  $new = new news($_GET['id']);

  if (isset($_POST['submit'])) {

    $live = (isset($_POST['live']) && $_POST['live'] == 'on');

    $new->modifyData(
      $_POST['title'],
      prepareText($_POST['text']),
      $live
    );

    $status = 'success';
    $message = 'Sikeresen frissítve!';

  }

  $newsData = $new->getData(true);

} else {

  $mode = 'list';

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
    'index_var' => $index_var,
    'news'      => isset($news) ? $news : null,
    'newsdata'  => isset($newsData) ? $newsData : null,
    'mode'      => isset($mode) ? $mode : null,
    'status'    => $status,
    'message'   => isset($message) ? $message : null
  )
);
