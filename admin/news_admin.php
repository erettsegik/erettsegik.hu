<?php

session_start();

$dir_level = 1;

require_once '../inc/functions.php';
require_once '../classes/news.class.php';

if (!checkRights(2)) {
    header('Location: /?p=login');
    die('Kerlek jelentkezz be.');
}

$twig = initTwig();

if (isset($_GET['action']) && $_GET['action'] == 'add') {

    if (isset($_POST['submit'])) {

        $status = 'submit';

        $new = new news();

        $new->insertData(
            $_POST['title'],
            $_POST['text']
        );

    } else {

        $status = 'form';

    }

} else if (isset($_GET['action']) && $_GET['action'] == 'edit') {

    $id = $_GET['id'];

    if (isset($_POST['submit'])) {

        $status = 'submit';

        $new = new news($id);

        $new->modifyData(
            $_POST['title'],
            $_POST['text']
        );

    } else {

        $status = 'form';

        $new = new news($id);

        $newsData = $new->getData();

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
