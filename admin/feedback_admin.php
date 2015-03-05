<?php

session_start();

$dir_level = 1;

require_once '../inc/functions.php';
require_once '../classes/feedback.class.php';

checkRights($config['clearance']['feedback']);

$user = new user($_SESSION['userid']);

$twig = initTwig();

if (isset($_GET['id'])) {

  $status = 'one';

  $feedback = new feedback($_GET['id']);
  $feedbackData = $feedback->getData();
  $feedback->makeNotNew();

} else {

  $status = 'all';

  try {

    $getFeedback = $con->prepare('
      select id
      from feedback
      order by id desc
    ');
    $getFeedback->execute();

  } catch (PDOException $e) {
    die($config['errors']['database']);
  }

  $feedbackArray = array();

  while ($feedbackData = $getFeedback->fetch()) {

    $feedback = new feedback($feedbackData['id']);
    $feedbackArray[] = $feedback->getData();

  }

}

echo $twig->render(
  'admin/feedback_admin.html',
  array(
    'feedbackarray' => isset($feedbackArray) ? $feedbackArray : null,
    'feedbackdata'  => isset($feedbackData) ? $feedbackData : null,
    'index_var'     => array(
      'menu'           => getAdminMenuItems(),
      'user_authority' => $user->getData()['authority']
    ),
    'status'        => $status
  )
);
