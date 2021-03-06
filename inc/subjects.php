<?php

require_once 'classes/note.class.php';
require_once 'classes/subject.class.php';

$index_var['location'][] = array(
  'url' => '/subjects/',
  'name' => 'Tantárgyak'
);

if (isset($_GET['id']) && isValid('subject', $_GET['id'])) {

  $subject = new subject($_GET['id']);

  $categories = array();

  try {

    $getNotesData = $con->prepare('
      select notes.id, notes.title, notes.incomplete, categories.name
      from notes
      inner join categories on notes.category = categories.id
      where notes.subjectid = :subjectid and notes.live = 1
      order by category asc, ordernumber asc, id asc
    ');
    $getNotesData->bindValue(
      'subjectid',
      $subject->getData()['id'],
      PDO::PARAM_INT
    );
    $getNotesData->execute();

  } catch (PDOException $e) {
    die($config['errors']['database']);
  }

  while ($notesData = $getNotesData->fetch()) {

    if (!isset($categories[$notesData['name']]))
      $categories[$notesData['name']] = array();

    if (!isset($categories[$notesData['name']]['data']))
      $categories[$notesData['name']]['data'] = array();

    $categories[$notesData['name']]['name'] = $notesData['name'];
    $categories[$notesData['name']]['data'][] = array(
      'id' => $notesData['id'],
      'title' => $notesData['title'],
      'incomplete' => $notesData['incomplete']
    );

  }

  $index_var['location'][] = array(
    'url' => '/subjects/' . $subject->getData()['id'] . '/',
    'name' => $subject->getData()['name']
  );

  $index_var['title'] = $subject->getData()['name'];

} else {

  $mode = 'all';

  $allsubjects = array();

  try {

    $getSubjectData = $con->query('
      select id from subjects
      order by mandatory desc, name asc
    ');

  } catch (PDOException $e) {
    die($config['errors']['database']);
  }

  while($subjectData = $getSubjectData->fetch()) {

    $subject = new subject($subjectData['id']);
    $allsubjects[] = $subject->getData();

  }

  $index_var['title'] = 'Tantárgyak';

}

echo $twig->render(
  'subjects.html',
  array(
    'allsubjects' => isset($allsubjects) ? $allsubjects : null,
    'categories'  => isset($categories) ? $categories :  null,
    'index_var'   => $index_var,
    'message'     => $message,
    'mode'        => isset($mode) ? $mode : null,
    'status'      => $status,
    'subject'     => (isset($mode) && $mode == 'all') ? null : $subject->getData()
  )
);
