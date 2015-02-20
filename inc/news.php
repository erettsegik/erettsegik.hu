<?php

require_once 'classes/event.class.php';
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

$current_events = array();

try {

  $getEvents = $con->query('
    select id
    from events
    where startdate <= now() and enddate > now()
    order by startdate asc
    limit 0, 5
  ');

} catch (PDOException $e) {
  die('Nem sikerült az események betöltése.');
}

while ($eventData = $getEvents->fetch()) {

  $event = new event($eventData['id']);
  $current_events[] = $event->getData();

}

$upcoming_events = array();

try {

  $getEvents = $con->query('
    select id
    from events
    where startdate >= now()
    order by startdate asc
    limit 0, 5
  ');

} catch (PDOException $e) {
  die('Nem sikerült az események betöltése.');
}

while ($eventData = $getEvents->fetch()) {

  $event = new event($eventData['id']);
  $upcoming_events[] = $event->getData();

}

echo $twig->render(
  'news.html',
  array(
    'index_var' => $index_var,
    'news'      => $news,
    'current_events' => $current_events,
    'upcoming_events' => $upcoming_events
  )
);
