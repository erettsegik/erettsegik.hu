<?php

require_once 'classes/feedback.class.php';

$index_var['location'][] = array('url' => '/feedback/', 'name' => 'Visszajelzés küldése');

$index_var['title'] = 'Visszajelzés küldése';

if (isset($_POST['submit'])) {

  $status = 'submit';

  if (isNotEmpty($_POST['title']) && isNotEmpty($_POST['text'])) {

    $feedback = new feedback();

    $feedback->insertData(
      prepareText($_POST['title']),
      prepareText($_POST['text'])
    );

  } else {

    $status = 'empty';

  }

} else {

  $status = 'form';

}

echo $twig->render(
  'feedback.html',
  array(
    'index_var' => $index_var,
    'status'    => $status
  )
);
