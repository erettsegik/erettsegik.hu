<?php

require_once 'classes/category.class.php';
require_once 'classes/modification.class.php';
require_once 'classes/note.class.php';
require_once 'classes/subject.class.php';

if (isset($_GET['action']) && $_GET['action'] == 'add') {

  if (isset($_POST['submit'])) {

    if (
      !isValid('subject', $_POST['subjectid']) ||
      !isValid('category', $_POST['category']) ||
      !isNotEmpty($_POST['title']) ||
      !isNotEmpty($_POST['text'])
    ) {

      $status = 'error';
      $message = 'Valamelyik mezőt nem helyesen töltötted ki!';

    } else {

      $note = new note();

      $note->insertData(
        prepareText($_POST['title']),
        prepareText($_POST['text']),
        $_POST['subjectid'],
        $_POST['category'],
        0,
        1
      );

      $_SESSION['status'] = 'success';
      $_SESSION['message'] = 'Köszönjük a beküldést, reméljük a jegyzet hamarosan megjelenik az oldalon!';

      header('Location: /subjects/' . $note->getData()['subjectid']);

    }

  }

  try {

    $getSubjects = $con->query('
      select id
      from subjects
      order by mandatory desc, name asc
    ');

  } catch (PDOException $e) {
    die('Nem sikerült a tantárgyak kiválasztása.');
  }

  $subjects = array();

  while ($subjectData = $getSubjects->fetch()) {

    $subject = new subject($subjectData['id']);
    $subjects[] = $subject->getData();

  }

  try {

    $getCategories = $con->query('select id from categories');

  } catch (PDOException $e) {
    die('Nem sikerült a kategóriák kiválasztása.');
  }

  $categories = array();

  while ($categoryData = $getCategories->fetch()) {

    $category = new category($categoryData['id']);
    $categories[] = $category->getData();

  }

  $index_var['location'][] = array(
    'url' => '/note/add/',
    'name' => 'Jegyzet beküldése'
  );

  $index_var['title'] = 'Jegyzet beküldése';

} else {

  $mode = 'display';

  if (!isValid('note', $_GET['id'])) {

    header('Location: /404/');

  }

  $note = new note($_GET['id']);

  $subject = new subject($note->getData()['subjectid']);

  $category = new category($note->getData()['category']);

  try {

    $getModificationsData = $con->prepare('
      select id
      from modifications
      where noteid = :noteid
    ');
    $getModificationsData->bindValue(
      'noteid',
      $note->getData()['id'],
      PDO::PARAM_INT
    );
    $getModificationsData->execute();

  } catch (PDOException $e) {
    die('Nem sikerült a kategóriák kiválasztása.');
  }

  $modifications = array();

  while ($modificationData = $getModificationsData->fetch()) {
    $modification = new modification($modificationData['id']);
    $modifications[] = $modification->getData();
  }

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
    'name' => (strlen($note->getData()['title']) > 17) ? substr($note->getData()['title'], 0, 37) . '...' : $note->getData()['title']
  );

  $index_var['title'] = $note->getData()['title'];

}

echo $twig->render(
  'note.html',
  array(
    'index_var'     => $index_var,
    'note'          => isset($note) ? $note->getData() : null,
    'modifications' => isset($modifications) ? $modifications : null,
    'mode'          => isset($mode) ? $mode : null,
    'message'       => isset($message) ? $message : null,
    'production'    => getenv('production') !== false ? true : false,
    'status'        => $status,
    'categories'    => isset($categories) ? $categories : null,
    'subjects'      => isset($subjects) ? $subjects : null
  )
);
