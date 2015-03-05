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
  die($config['errors']['database']);
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

  $getCurrentEvents = $con->query('
    select id
    from events
    where startdate <= now() and enddate >= now()
    order by startdate asc
    limit 0, 5
  ');

} catch (PDOException $e) {
  die($config['errors']['database']);
}

while ($eventData = $getCurrentEvents->fetch()) {

  $event = new event($eventData['id']);
  $current_events[] = $event->getData();

}

$upcoming_events = array();

try {

  $getUpcomingEvents = $con->query('
    select id
    from events
    where startdate >= now()
    order by startdate asc
    limit 0, 5
  ');

} catch (PDOException $e) {
  die($config['errors']['database']);
}

while ($eventData = $getUpcomingEvents->fetch()) {

  $event = new event($eventData['id']);
  $upcoming_events[] = $event->getData();

}

echo $twig->render(
  'news.html',
  array(
    'current_events'  => $current_events,
    'index_var'       => $index_var,
    'message'         => isset($message) ? $message : null,
    'news'            => $news,
    'status'          => $status,
    'upcoming_events' => $upcoming_events
  )
);
