<?php

require_once 'classes/news.class.php';

$index_var['location'][] = array('url' => '?p=news', 'name' => 'Hírek');

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
        'index_var' => $index_var,
        'news' => $news
    )
);
