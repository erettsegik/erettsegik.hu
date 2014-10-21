<?php

require_once 'classes/news.class.php';

$news = array();

$getNewsData = $con->query('select id from news order by date desc');

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
