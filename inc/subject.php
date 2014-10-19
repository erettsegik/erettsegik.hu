<?php

require_once 'classes/note.class.php';
require_once 'classes/subject.class.php';

$subject = new subject($_GET['id']);

$categories = array();

$getNotesData = $con->prepare('
    select notes.id, notes.title, categories.name
    from notes
    left join categories on notes.category = categories.id
    where notes.subjectid = :subjectid
');
$getNotesData->bindValue('subjectid', $subject->getData()['id'], PDO::PARAM_INT);
$getNotesData->execute();

while ($notesData = $getNotesData->fetch()) {

    if (!isset($categories[$notesData['name']]))
        $categories[$notesData['name']] = array();

    if (!isset($categories[$notesData['name']]['data']))
        $categories[$notesData['name']]['data'] = array();

    $categories[$notesData['name']]['name'] = $notesData['name'];
    $categories[$notesData['name']]['data'][] = array(
        'id' => $notesData['id'],
        'title' => $notesData['title']
    );

}

echo $twig->render(
    'subject.html',
    array(
        'subjects' => $subjects,
        'subject' => $subject->getData(),
        'categories' => $categories
    )
);
