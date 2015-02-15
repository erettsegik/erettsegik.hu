<?php

require_once 'classes/news.class.php';
require_once 'classes/user.class.php';

$index_var['location'][] = array('url' => '/news/', 'name' => 'Hírek');

$index_var['title'] = 'Hírek';

$news = array();

try {

  $getNewsData = $con->query('
    select id
    from news
    where live = 1
    order by date desc
  ');

} catch (PDOException $e) {
  die('Nem sikerült a híreket betölteni.');
}

while ($newsData = $getNewsData->fetch()) {

  $new = new news($newsData['id']);
  $a = $new->getData();

  $author = new user($new->getData()['creatorid']);
  $a['creator'] = $author->getData()['name'];

  $news[] = $a;

}

echo $twig->render(
  'news.html',
  array(
    'index_var' => $index_var,
    'news'      => $news
  )
);
