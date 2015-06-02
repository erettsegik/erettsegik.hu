<?php

require_once 'classes/category.class.php';
require_once 'classes/logger.class.php';
require_once 'classes/modification.class.php';
require_once 'classes/note.class.php';
require_once 'classes/subject.class.php';

if (isset($_GET['action']) && $_GET['action'] == 'add') {

  if (isset($_POST['submit'])) {

    $recaptcha = new \ReCaptcha\ReCaptcha($config['recaptcha-key']);
    $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

    if (
      !isValid('subject', $_POST['subjectid']) ||
      !isValid('category', $_POST['category']) ||
      !isNotEmpty($_POST['title']) ||
      !isNotEmpty($_POST['text']) || !$resp->isSuccess()
    ) {

      $saved = array('title' => $_POST['title'], 'text' => $_POST['text'], 'subjectid' => $_POST['subjectid'], 'category' => $_POST['category'], 'email' => $_POST['email']);

      $status = 'error';
      $message = $resp->isSuccess() ? 'Valamelyik mezőt nem helyesen töltötted ki!' : 'Robot vagy?';

    } else {

      $note = new note();

      $note->insertData(
        prepareText($_POST['title']),
        prepareText($_POST['text']),
        prepareText($_POST['footnotes']),
        $_POST['subjectid'],
        $_POST['category'],
        0,
        1,
        $_POST['email']
      );

      $logger = new logger();

      $logger->log('Note submitted');

      $_SESSION['status'] = 'success';
      $_SESSION['message'] = 'Köszönjük a beküldést, reméljük a jegyzet hamarosan megjelenik az oldalon!';

      header('Location: /subjects/' . $note->getData()['subjectid']);
      die();

    }

  }

  try {

    $getSubjects = $con->query('
      select id
      from subjects
      order by mandatory desc, name asc
    ');

  } catch (PDOException $e) {
    die($config['errors']['database']);
  }

  $subjects = array();

  while ($subjectData = $getSubjects->fetch()) {

    $subject = new subject($subjectData['id']);
    $subjects[] = $subject->getData();

  }

  try {

    $getCategories = $con->query('select id from categories');

  } catch (PDOException $e) {
    die($config['errors']['database']);
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
    die();

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
    die($config['errors']['database']);
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
    'name' => (strlen($note->getData()['title']) > 37) ? mb_substr($note->getData()['title'], 0, 37, 'utf-8') . '...' : $note->getData()['title']
  );

  $index_var['title'] = $note->getData()['title'];

  if ($note->getData()['incomplete']) {
    $status = 'notice';
    $message = 'Ez a jegyzet félkész. Kérjük, segíts kibővíteni egy javaslat beküldésével!';
  }

}

$rendertarget = $_SESSION['mobile'] ? 'mobile/note.html' : 'note.html';

echo $twig->render(
  $rendertarget,
  array(
    'categories'    => isset($categories) ? $categories : null,
    'index_var'     => $index_var,
    'message'       => $message,
    'mode'          => isset($mode) ? $mode : null,
    'modifications' => isset($modifications) ? $modifications : null,
    'note'          => isset($note) ? $note->getData() : null,
    'production'    => getenv('production') !== false,
    'saved'         => isset($saved) ? $saved : null,
    'status'        => $status,
    'subjects'      => isset($subjects) ? $subjects : null
  )
);
