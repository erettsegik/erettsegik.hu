<?php

require_once 'classes/feedback.class.php';
require_once 'classes/logger.class.php';

$index_var['location'][] = array('url' => '/feedback/', 'name' => 'Visszajelzés küldése');

$index_var['title'] = 'Visszajelzés küldése';

if (isset($_POST['submit'])) {

  if (isNotEmpty($_POST['title']) && isNotEmpty($_POST['text'])) {

    $feedback = new feedback();

    $feedback->insertData(
      prepareText($_POST['title']),
      prepareText($_POST['text'])
    );

    $logger = new logger();

    $logger->log('Feedback sent');

    $_SESSION['status'] = 'success';
    $_SESSION['message'] = 'Köszönjük a visszajelzést!';

    header('Location: /');
    die();

  }

  $saved = array('title' => $_POST['title'], 'text' => $_POST['text']);

  $status = 'error';
  $message = 'Nem küldheted el üresen az űrlapot!';

}

$rendertarget = $_SESSION['mobile'] ? 'mobile/feedback.html' : 'feedback.html';

echo $twig->render(
  $rendertarget,
  array(
    'index_var' => $index_var,
    'message'   => $message,
    'saved'     => isset($saved) ? $saved : null,
    'status'    => $status
  )
);
