<?php

require_once 'classes/category.class.php';
require_once 'classes/modification.class.php';
require_once 'classes/note.class.php';

if (isset($_GET['id'])) {

    if (isValid('modification', $_GET['id'])) {

        $modification = new modification($_GET['id']);

        $noteid = $modification->getData()['noteid'];

    } else {
        die('Érvénytelen');
    }

}

if (!isValid('note', $_GET['noteid']) && !isset($_GET['id'])) {
    die('Érvénytelen!');
}

if (!isset($noteid)) {
    $noteid = $_GET['noteid'];
}

$note = new note($noteid);

$subject = new subject($note->getData()['subjectid']);

$category = new category($note->getData()['category']);

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

if (isset($_GET['action']) && $_GET['action'] == 'add') {

    $index_var['title'] = 'Javaslat hozzáadása';

    $index_var['location'][] = array(
        'url' => '?p=modification',
        'name' => 'Javaslat hozzáadása'
    );

    $note = new note($_GET['noteid']);

    if (isset($_POST['submit'])) {

        $status = 'submit';

        if (isNotEmpty($_POST['title']) && isNotEmpty($_POST['new_text'])) {

            $modification = new modification();

            $modification->insertData(
                $_GET['noteid'],
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
        'url' => '?p=modification',
        'name' => 'Javaslat: ' . $modification->getData()['title']
    );

    $index_var['title'] = $modification->getData()['title'];

    $note = new note($modification->getData()['noteid']);

}

echo $twig->render(
    'modification.html',
    array(
        'index_var' => $index_var,
        'modification' => isset($modification) ? $modification->getData() : null,
        'action' => isset($_GET['action']) ? $_GET['action'] : null,
        'note' => $note->getData(),
        'status' => $status,
        'diff' => isset($diff) ? $diff : null
    )
);
