<?php

require_once 'classes/note.class.php';
require_once 'classes/subject.class.php';

$subject = new subject($_GET['id']);

$levels = array();

$getNotesData = $con->prepare('
    select id, title, level
    from notes
    where subjectid = :subjectid
');
$getNotesData->bindValue('subjectid', $subject->getData()['id'], PDO::PARAM_INT);
$getNotesData->execute();

while ($notesData = $getNotesData->fetch()) {
    $levels[$notesData['level']][] = array('id' => $notesData['id'], 'title' => $notesData['title']);
}

echo $twig->render(
    'subject.html',
    array(
        'subjects' => $subjects,
        'subject' => $subject->getData(),
        'levels' => $levels,
        'levelnames' => array(0 => 'Közép és emelt', 1 => 'Közép', 2 => 'Emelt', )
    )
);
