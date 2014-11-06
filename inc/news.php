<?php

require_once 'classes/news.class.php';

$news = array();

try {

    $getNewsData = $con->query('select id from news order by date desc');

} catch (PDOException $e) {
    die('Nem sikerült a híreket betölteni.');
}

while ($newsData = $getNewsData->fetch()) {

    $new = new news($newsData['id']);
    $news[] = $new->getData();

}

echo $twig->render(
    'news.html',
    array(
        'subjects' => $subjects,
        'news' => $news
    )
);
