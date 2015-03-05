<?php

require_once 'classes/category.class.php';
require_once 'classes/note.class.php';
require_once 'classes/subject.class.php';

$index_var['location'][] = array('url' => '/search/', 'name' => 'Keresés');

$index_var['title'] = 'Keresés';

if (isset($_GET['term']) && isNotEmpty($_GET['term'])) {

  if (strlen($_GET['term']) < 3) {

    $status = 'error';
    $message = 'Adj meg legalább három karaktert a kereséshez!';

  } else {

    $term = $_GET['term'];

    $noteResults = array();

    try {

      $titleSearch = $con->prepare('
        select id
        from notes
        where title like :term
        order by subjectid asc, id asc
      ');
      $titleSearch->bindValue('term', '%' . $term . '%', PDO::PARAM_STR);
      $titleSearch->execute();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

    try {

      $textSearch = $con->prepare('
        select id
        from notes
        where text like :term
        order by subjectid asc, id asc
      ');
      $textSearch->bindValue('term', '%' . $term . '%', PDO::PARAM_STR);
      $textSearch->execute();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

    while ($titleResult = $titleSearch->fetch()) {

      $note = new note($titleResult['id']);
      $noteData = $note->getData();

      $subject = new subject($noteData['subjectid']);
      $category = new category($noteData['category']);

      $result = array(
        'id' => $titleResult['id'],
        'title' => $noteData['title'],
        'subject' => $subject->getData()['name'],
        'category' => $category->getData()['name']
      );

      if (!in_array($result, $noteResults)) {
        $noteResults[] = $result;
      }

    }

    while ($textResult = $textSearch->fetch()) {

      $note = new note($textResult['id']);
      $noteData = $note->getData();

      $subject = new subject($noteData['subjectid']);
      $category = new category($noteData['category']);

      $result = array(
        'id' => $textResult['id'],
        'title' => $noteData['title'],
        'subject' => $subject->getData()['name'],
        'category' => $category->getData()['name']
      );

      if (!in_array($result, $noteResults)) {
        $noteResults[] = $result;
      }

    }

    $mode = 'results';

  }

}

echo $twig->render(
  'search.html',
  array(
    'index_var'   => $index_var,
    'message'     => isset($message) ? $message : null,
    'mode'        => isset($mode) ? $mode : null,
    'resultcount' => isset($noteResults) ? count($noteResults) : null,
    'results'     => isset($noteResults) ? $noteResults : null,
    'status'      => isset($status) ? $status : null,
    'term'        => isset($term) ? $term : null
  )
);
