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

    $intitle = isset($_POST['intitle']) && $_POST['intitle'] == 'on';
    $intext = isset($_POST['intext']) && $_POST['intext'] == 'on';

    if ($intitle) {

      try {

        $titleSearch = $con->prepare('
          select id
          from notes
          where title like :term and live = 1 and incomplete <> 2
          order by subjectid asc, id asc
        ');
        $titleSearch->bindValue('term', '%' . $term . '%', PDO::PARAM_STR);
        $titleSearch->execute();

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

    }

    if ($intext) {

      try {

        $textSearch = $con->prepare('
          select id
          from notes
          where text like :term and live = 1 and incomplete <> 2
          order by subjectid asc, id asc
        ');
        $textSearch->bindValue('term', '%' . $term . '%', PDO::PARAM_STR);
        $textSearch->execute();

      } catch (PDOException $e) {
        die($config['errors']['database']);
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

    }

    if ($intitle || $intext)
      $mode = 'results';

  }

}

echo $twig->render(
  'search.html',
  array(
    'index_var'   => $index_var,
    'message'     => $message,
    'mode'        => isset($mode) ? $mode : null,
    'resultcount' => isset($noteResults) ? count($noteResults) : null,
    'results'     => isset($noteResults) ? $noteResults : null,
    'status'      => $status,
    'term'        => isset($term) ? $term : null,
    'intitle'     => isset($intitle) ? $intitle : null,
    'intext'     => isset($intext) ? $intext : null,
  )
);
