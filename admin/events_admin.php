<?php

session_start();

$dir_level = 1;

require_once '../inc/functions.php';
require_once '../classes/event.class.php';

checkRights($config['clearance']['events']);

$user = new user($_SESSION['userid']);

$twig = initTwig();

if (isset($_GET['action']) && $_GET['action'] == 'add') {

  if (isset($_POST['submit'])) {

    $event = new event();

    $event->insertData($_POST['name'], $_POST['startdate'], $_POST['enddate']);

    header('Location: /admin/events_admin.php');

  }

} else if (isset($_GET['action']) && $_GET['action'] == 'edit') {

  $event = new event($_GET['id']);

  if (isset($_POST['submit'])) {

    $event->modifyData($_POST['name'], $_POST['startdate'], $_POST['enddate']);

  }

  $eventdata = $event->getData();

  $eventdata['startdate'] = str_replace(' ', 'T', $eventdata['startdate']);
  $eventdata['enddate'] = str_replace(' ', 'T', $eventdata['enddate']);

} else {

  $status = 'list';

  $events = array();

  try {

    $selectEvents = $con->query('
      select id, name
      from events
      order by startdate asc
    ');

  } catch (PDOException $e) {
    die('Hiba.');
  }

  while ($event = $selectEvents->fetch()) {
    $events[] = array('id' => $event['id'], 'name' => $event['name']);
  }

}

echo $twig->render(
  'admin/events_admin.html',
  array(
    'action' => (isset($_GET['action']) ? $_GET['action'] : null),
    'current_dt' => str_replace(' ', 'T', date('Y-m-d H:i')),
    'eventdata' => (isset($eventdata)) ? $eventdata : null,
    'events' => (isset($events)) ? $events : null,
    'status' => $status,
    'index_var' => array(
      'menu' => getAdminMenuItems(),
      'user_authority' => $user->getData()['authority']
    )
  )
);
