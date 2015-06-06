<?php

require_once 'classes/event.class.php';
require_once 'classes/news.class.php';
require_once 'classes/note.class.php';
require_once 'classes/category.class.php';
require_once 'classes/subject.class.php';
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

$recentNotes = array();

try {

  $getNotes = $con->query('
    select id
    from notes
    where live = 1 and incomplete <> 2
    order by updatedate desc
    limit 5
  ');

} catch (PDOException $e) {
  die($config['errors']['database']);
}

while ($foundNote = $getNotes->fetch()) {

  $note = new note($foundNote['id']);
  $noteData = $note->getData();

  $subject = new subject($noteData['subjectid']);
  $category = new category($noteData['category']);

  $result = array(
    'id' => $noteData['id'],
    'title' => $noteData['title'],
    'subject' => $subject->getData()['name'],
    'subjectid' => $noteData['subjectid'],
    'category' => $category->getData()['name']
  );

  $recentNotes[] = $result;

}

$rendertarget = $_SESSION['mobile'] ? 'mobile/news.html' : 'news.html';

echo $twig->render(
  $rendertarget,
  array(
    'current_events'  => $current_events,
    'index_var'       => $index_var,
    'message'         => $message,
    'news'            => $news,
    'recentnotes'     => $recentNotes,
    'status'          => $status,
    'upcoming_events' => $upcoming_events
  )
);
