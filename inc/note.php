<?php

require_once 'classes/category.class.php';
require_once 'classes/modification.class.php';
require_once 'classes/note.class.php';

if (isset($_GET['action']) && $_GET['action'] == 'add') {

    if (isset($_POST['submit'])) {

        $status = 'addsubmit';

        $note = new note();

        $note->insertData(
            $_POST['title'],
            $_POST['text'],
            $_POST['subjectid'],
            $_POST['category'],
            0
        );

    } else {

        $status = 'addform';

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

} else {

    $status = 'display';

    $note = new note($_GET['id']);

    $getModificationsData = $con->prepare('
        select id
        from modifications
        where noteid = :noteid
    ');
    $getModificationsData->bindValue('noteid', $note->getData()['id'], PDO::PARAM_INT);
    $getModificationsData->execute();

    $modifications = array();

    while ($modificationData = $getModificationsData->fetch()) {
        $modification = new modification($modificationData['id']);
        $modifications[] = $modification->getData();
    }

}

echo $twig->render(
    'note.html',
    array(
        'subjects' => $subjects,
        'note' => (isset($note)) ? $note->getData() : null,
        'modifications' => (isset($modifications)) ? $modifications : null,
        'status' => $status,
        'categories' => (isset($categories)) ? $categories : null
    )
);
