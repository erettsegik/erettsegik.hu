<?php

require_once '../classes/category.class.php';
require_once '../classes/note.class.php';

checkRights($config['clearance']['notes']);

if (isset($_GET['action']) && $_GET['action'] == 'add') {

  if (isset($_POST['submit'])) {

    $note = new note();

    $live = (isset($_POST['live']) && $_POST['live'] == 'on');

    $incomplete = (isset($_POST['incomplete']) && $_POST['incomplete'] == 'on');

    $note->insertData(
      $_POST['title'],
      prepareText($_POST['text']),
      $_POST['subjectid'],
      $_POST['category'],
      $live,
      $incomplete,
      null
    );

    $redirect_string = 'Location: index.php?p=notes_admin';

    if (isset($_GET['subjectid'])) {
      $redirect_string .= '?subjectid=' . $_GET['subjectid'];
    }

    $_SESSION['status'] = 'success';
    $_SESSION['message'] = 'Sikeres mentés!';

    header($redirect_string);

  }

} else if (isset($_GET['action']) && $_GET['action'] == 'edit') {

  $note = new note($_GET['id']);

  if (isset($_POST['submit'])) {

    if (isset($_POST['delete']) && $_POST['delete'] == 'on') {

      $note->remove();

      $_SESSION['status'] = 'success';
      $_SESSION['message'] = 'Sikeres törlés!';

      $redirect_string = 'Location: index.php?p=notes_admin';

      header($redirect_string);

    } else {

      $live = (isset($_POST['live']) && $_POST['live'] == 'on');

      $incomplete = (isset($_POST['incomplete']) && $_POST['incomplete'] == 'on');

      $note->modifyData(
        $_POST['title'],
        prepareText($_POST['text']),
        $_POST['subjectid'],
        $_POST['category'],
        $live,
        $incomplete
      );

      $status = 'success';
      $message = 'Sikeres mentés!';

    }

  }

  $noteData = $note->getData(true);

} else {

  if (isset($_GET['subjectid'])) {

    if (isset($_POST['updateorder'])) {

      try {

        $getNotes = $con->prepare('
          select id
          from notes
          where subjectid = :subjectid
        ');
        $getNotes->bindValue('subjectid', $_GET['subjectid'], PDO::PARAM_INT);
        $getNotes->execute();

      } catch (PDOException $e) {
        die($config['errors']['database']);
      }

      while ($noteData = $getNotes->fetch()) {

        $note = new note($noteData['id']);
        $note->modifyOrder($_POST[$noteData['id'] . 'order']);

        $status = 'success';
        $message = 'Sikeres frissítés!';

      }

    }

    $mode = 'notelist';

    $notes = array();

    try {

      $getNotes = $con->prepare('
        select notes.id, notes.title, notes.ordernumber, notes.live, notes.incomplete, categories.name
        from notes
        left join categories on notes.category = categories.id
        where notes.subjectid = :subjectid
        order by notes.ordernumber asc, notes.id asc
      ');
      $getNotes->bindValue('subjectid', $_GET['subjectid'], PDO::PARAM_INT);
      $getNotes->execute();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

    while ($noteData = $getNotes->fetch()) {

      if (!isset($noteList[$noteData['name']])) {
        $noteList[$noteData['name']] = array();
      }

      if (!isset($noteList[$noteData['name']]['data'])) {
        $noteList[$noteData['name']]['data'] = array();
      }

      $noteList[$noteData['name']]['name'] = $noteData['name'];
      $noteList[$noteData['name']]['data'][] = array(
        'id' => $noteData['id'],
        'title' => $noteData['title'],
        'ordernumber' => $noteData['ordernumber'],
        'live' => $noteData['live'],
        'incomplete' => $noteData['incomplete']
      );

    }

  } else {

    $mode = 'subjectlist';

  }

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

if (isset($_GET['subjectid'])) {

  $subject = new subject($_GET['subjectid']);
  $selectedSubject = $subject->getData();

} else {
  $selectedSubject = array('id' => 0);
}

echo $twig->render(
  'admin/notes_admin.html',
  array(
    'action'          => isset($_GET['action']) ? $_GET['action'] : null,
    'categories'      => $categories,
    'index_var'       => $index_var,
    'saved'           => isset($noteData) ? $noteData : array('subjectid' => $selectedSubject['id']),
    'notelist'        => isset($noteList) ? $noteList : null,
    'selectedsubject' => $selectedSubject,
    'mode'            => isset($mode) ? $mode : null,
    'subjects'        => getSubjects(),
    'status'          => $status,
    'message'         => $message
  )
);

function getSubjects() {

  global $con;
  global $config;

  $subjects = array();

  try {

    $getSubjects = $con->query('
      select id, name
      from subjects
      order by mandatory desc, name asc
    ');

  } catch (PDOException $e) {
    die($config['errors']['database']);
  }

  while ($subject = $getSubjects->fetch()) {
    $subjects[] = $subject;
  }

  return $subjects;

}
