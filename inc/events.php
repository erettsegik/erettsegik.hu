<?php

require_once 'classes/event.class.php';

$index_var['location'][] = array('url' => '/events/', 'name' => 'Események');

$index_var['title'] = 'Események';

$past_events = array();

try {

  $getPastEvents = $con->query('
    select id
    from events
    where enddate < now()
    order by startdate asc
  ');

} catch (PDOException $e) {
  die('Nem sikerült az események betöltése.');
}

while ($eventData = $getPastEvents->fetch()) {

  $event = new event($eventData['id']);
  $past_events[] = $event->getData();

}

$current_events = array();

try {

  $getCurrentEvents = $con->query('
    select id
    from events
    where startdate <= now() and enddate >= now()
    order by startdate asc
  ');

} catch (PDOException $e) {
  die('Nem sikerült az események betöltése.');
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
  ');

} catch (PDOException $e) {
  die('Nem sikerült az események betöltése.');
}

while ($eventData = $getUpcomingEvents->fetch()) {

  $event = new event($eventData['id']);
  $upcoming_events[] = $event->getData();

}

echo $twig->render(
  'events.html',
  array(
    'index_var' => $index_var,
    'past_events' => $past_events,
    'current_events' => $current_events,
    'upcoming_events' => $upcoming_events
  )
);
