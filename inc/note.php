<?php

require_once 'classes/category.class.php';
require_once 'classes/modification.class.php';
require_once 'classes/note.class.php';
require_once 'classes/subject.class.php';

if (isset($_GET['action']) && $_GET['action'] == 'add') {

    if (isset($_POST['submit'])) {

        $status = 'addsubmit';

        if (
            !isValid('subject', $_POST['subjectid']) ||
            !isValid('category', $_POST['category']) ||
            trim($_POST['title']) == '' ||
            trim($_POST['text']) == ''
        ) {

            $status = 'notvalid';

        } else {

            $note = new note();

            $note->insertData(
                prepareText($_POST['title']),
                prepareText($_POST['text']),
                $_POST['subjectid'],
                $_POST['category'],
                0
            );

        }

    } else {

        $status = 'addform';

    }

    try {

      $getSubjects = $con->query('select id from subjects');

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
        'url' => '?p=note?action=&amp;add',
        'name' => 'Jegyzet hozzáadása'
    );

    $index_var['title'] = 'Jegyzet hozzáadása';

} else {

    $status = 'display';

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
        'url' => '?p=subject',
        'name' => 'Tantárgyak'
    );

    $index_var['location'][] = array(
        'url' => '?p=subject&id=' . $subject->getData()['id'],
        'name' => $subject->getData()['name']
    );

    $index_var['location'][] = array(
        'url' => '?p=subject&id=' . $subject->getData()['id'] . '#' . $category->getData()['name'],
        'name' => $category->getData()['name']
    );

    $index_var['location'][] = array(
        'url' => '?p=note&id=' . $note->getData()['id'],
        'name' => $note->getData()['title']
    );

    $index_var['title'] = $note->getData()['title'];

}

echo $twig->render(
    'note.html',
    array(
        'index_var' => $index_var,
        'note' => (isset($note)) ? $note->getData() : null,
        'modifications' => (isset($modifications)) ? $modifications : null,
        'status' => $status,
        'categories' => (isset($categories)) ? $categories : null,
        'subjects' => (isset($subjects)) ? $subjects : null
    )
);
