<?php

session_start();

$dir_level = 1;

require_once '../inc/functions.php';
require_once '../classes/event.class.php';

checkRights($config['clearance']['events']);

$user = new user($_SESSION['userid']);

$twig = initTwig();

$status = 'none';

if (isset($_GET['action']) && $_GET['action'] == 'add') {

  if (isset($_POST['submit'])) {

    $event = new event();

    $event->insertData($_POST['name'], $_POST['startdate'], $_POST['enddate']);

    header('Location: /admin/events_admin.php');

  }

} else if (isset($_GET['action']) && $_GET['action'] == 'edit') {

  $event = new event($_GET['id']);

  if (isset($_POST['submit'])) {

    if (isset($_POST['delete']) && $_POST['delete'] == 'on') {

      $event->remove();

      header('Location: /admin/events_admin.php');

    } else {

      $event->modifyData($_POST['name'], $_POST['startdate'], $_POST['enddate']);

    }

  }

  $eventdata = $event->getData(true);

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
    die($config['errors']['database']);
  }

  while ($event = $selectEvents->fetch()) {
    $events[] = array('id' => $event['id'], 'name' => $event['name']);
  }

}

$current = new DateTime(null, $config['tz']['utc']);
$current->setTimeZone($config['tz']['local']);
$current_dt = $current->format($config['htmldate']);

echo $twig->render(
  'admin/events_admin.html',
  array(
    'action'     => isset($_GET['action']) ? $_GET['action'] : null,
    'current_dt' => $current_dt,
    'eventdata'  => isset($eventdata) ? $eventdata : null,
    'events'     => isset($events) ? $events : null,
    'index_var'  => array(
      'menu'           => getAdminMenuItems(),
      'user_authority' => $user->getData()['authority']
    ),
    'status'     => $status
  )
);
