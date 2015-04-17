<?php

require_once '../classes/feedback.class.php';

checkRights($config['clearance']['feedback']);

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
    'index_var'     => $index_var,
    'status'        => $status
  )
);
