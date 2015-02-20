<?php

require_once 'classes/category.class.php';
require_once 'classes/modification.class.php';
require_once 'classes/note.class.php';

if (isset($_GET['action']) && $_GET['action'] == 'add') {

  if (isset($_GET['id']) && isValid('note', $_GET['id'])) {

    $noteid = $_GET['id'];

  } else {
    header('Location: /404/');
  }

}

if (isset($_GET['id']) && !isset($_GET['action'])) {

  if (isValid('modification', $_GET['id'])) {

    $modification = new modification($_GET['id']);

    $noteid = $modification->getData()['noteid'];

  } else {
    header('Location: /404/');
  }

}

$note = new note($noteid);
$subject = new subject($note->getData()['subjectid']);
$category = new category($note->getData()['category']);

$index_var['location'][] = array(
  'url' => '/subjects/',
  'name' => 'Tantárgyak'
);

$index_var['location'][] = array(
  'url' => '/subjects/' . $subject->getData()['id'] . '/',
  'name' => $subject->getData()['name']
);

$index_var['location'][] = array(
  'url' => '/subjects/' . $subject->getData()['id'] . '/#' . $category->getData()['name'],
  'name' => $category->getData()['name']
);

$index_var['location'][] = array(
  'url' => '/note/' . $note->getData()['id'] . '/',
  'name' => (strlen($note->getData()['title']) > 17) ? substr($note->getData()['title'], 0, 17) . '...' : $note->getData()['title']
);

if (isset($_GET['action']) && $_GET['action'] == 'add') {

  $index_var['title'] = 'Javaslat hozzáadása';

  $index_var['location'][] = array(
    'url' => '/modification/',
    'name' => 'Javaslat hozzáadása'
  );

  $note = new note($_GET['id']);

  if (isset($_POST['submit'])) {

    $status = 'submit';

    if (isNotEmpty($_POST['title']) && isNotEmpty($_POST['new_text'])) {

      $modification = new modification();

      $modification->insertData(
        $_GET['id'],
        $_POST['title'],
        $_POST['new_text'],
        $_POST['comment']
      );

    } else {
      die('Érvénytelen!');
    }

  } else {

    $status = 'form';

  }

} else {

  $status = 'display';

  $modification = new modification($_GET['id']);

  $index_var['location'][] = array(
    'url' => '/modification/',
    'name' => (strlen($modification->getData()['title']) > 17) ? 'Javaslat: ' . substr($modification->getData()['title'], 0, 17) . '...' : 'Javaslat: ' . $modification->getData()['title']
  );

  $index_var['title'] = $modification->getData()['title'];

  $note = new note($modification->getData()['noteid']);

}

echo $twig->render(
  'modification.html',
  array(
    'index_var'    => $index_var,
    'modification' => isset($modification) ? $modification->getData() : null,
    'action'       => isset($_GET['action']) ? $_GET['action'] : null,
    'note'         => $note->getData(),
    'status'       => $status,
    'diff'         => isset($diff) ? $diff : null
  )
);
