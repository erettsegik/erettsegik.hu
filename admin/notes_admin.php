<?php

require_once '../classes/category.class.php';
require_once '../classes/logger.class.php';
require_once '../classes/note.class.php';

checkRights($config['clearance']['notes']);

if (isset($_GET['action']) && $_GET['action'] == 'add') {

  if (isset($_POST['submit'])) {

    $note = new note();

    $live = (isset($_POST['live']) && $_POST['live'] == 'on');

    $incomplete = (isset($_POST['incomplete']) && $_POST['incomplete'] == 'on');

    if (prepareText($_POST['text']) == '') $incomplete = 2;

    $note->insertData(
      $_POST['title'],
      prepareText($_POST['text']),
      prepareText($_POST['footnotes']),
      $_POST['subjectid'],
      $_POST['category'],
      $live,
      $incomplete,
      '',
      $_SESSION['userid']
    );

    $logger = new logger();

    $logger->log($user->getData()['name'] . ' added a new note');

    $noteData = $note->getData();

    $subjectid = $noteData['subjectid'];
    $category = new category($noteData['category']);

    $redirect_string = 'Location: index.php?p=notes_admin&subjectid=' . $subjectid . '#' . $category->getData()['name'];

    $_SESSION['status'] = 'success';
    $_SESSION['message'] = 'Sikeres mentés!';

    header($redirect_string);

  }

} else if (isset($_GET['action']) && $_GET['action'] == 'edit') {

  $note = new note($_GET['id']);

  if (isset($_POST['submit'])) {

    if (isset($_POST['delete']) && $_POST['delete'] == 'on') {

      $noteData = $note->getData();

      $subjectid = $noteData['subjectid'];
      $category = new category($noteData['category']);

      $note->remove();

      $logger = new logger();

      $logger->log($user->getData()['name'] . ' removed a note: ' . $note->getData()['id'] . ' - ' . $note->getData()['title']);

      $_SESSION['status'] = 'success';
      $_SESSION['message'] = 'Sikeres törlés!';

      $redirect_string = 'Location: index.php?p=notes_admin&subjectid=' . $subjectid . '#' . $category->getData()['name'];

      header($redirect_string);

    } else {

      $live = (isset($_POST['live']) && $_POST['live'] == 'on');

      $incomplete = (isset($_POST['incomplete']) && $_POST['incomplete'] == 'on');

      if (prepareText($_POST['text']) == '') $incomplete = 2;

      $note->modifyData(
        $_POST['title'],
        prepareText($_POST['text']),
        prepareText($_POST['footnotes']),
        $_POST['subjectid'],
        $_POST['category'],
        $live,
        $incomplete
      );

      $logger = new logger();

      $logger->log($user->getData()['name'] . ' edited a note: ' . $note->getData()['id']);

      $status = 'success';
      $message = 'Sikeres mentés!';

      $noteData = $note->getData();

      $subjectid = $noteData['subjectid'];
      $category = new category($noteData['category']);

      $redirect_string = 'Location: index.php?p=notes_admin&subjectid=' . $subjectid . '#' . $category->getData()['name'];

      header($redirect_string);

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
        inner join categories on notes.category = categories.id
        where notes.subjectid = :subjectid
        order by notes.category asc, notes.ordernumber asc, notes.id asc
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

    $recentNotes = array();

    try {

      $getNotes = $con->query('
        select id
        from notes
        where live = 0 and incomplete <> 0
        order by updatedate desc
        limit 10
      ');

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

    while ($foundNote = $getNotes->fetch()) {

      $note = new note($foundNote['id']);
      $noteData = $note->getData();

      $subject = new subject($noteData['subjectid']);
      $category = new category($noteData['category']);

      $result = array(
        'id' => $noteData['id'],
        'title' => $noteData['title'],
        'updatedate' => $noteData['updatedate'],
        'subject' => $subject->getData()['name'],
        'subjectid' => $noteData['subjectid'],
        'category' => $category->getData()['name']
      );

      $recentNotes[] = $result;

    }

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
    'message'         => $message,
    'recentnotes'     => isset($recentNotes) ? $recentNotes : null
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
