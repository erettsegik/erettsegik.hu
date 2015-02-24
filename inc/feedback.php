<?php

require_once 'classes/feedback.class.php';

$index_var['location'][] = array('url' => '/feedback/', 'name' => 'Visszajelzés küldése');

$index_var['title'] = 'Visszajelzés küldése';

if (isset($_POST['submit'])) {

  if (isNotEmpty($_POST['title']) && isNotEmpty($_POST['text'])) {

    $feedback = new feedback();

    $feedback->insertData(
      prepareText($_POST['title']),
      prepareText($_POST['text'])
    );

    $_SESSION['status'] = 'success';
    $_SESSION['message'] = 'Köszönjük a visszajelzést!';

    header('Location: /');

  } else {

    $status = 'error';
    $message = 'Nem küldheted el üresen az űrlapot!';

  }

}

echo $twig->render(
  'feedback.html',
  array(
    'index_var' => $index_var,
    'status'    => $status,
    'message'   => isset($message) ? $message : null
  )
);
