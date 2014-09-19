<?php

require_once 'classes/note.class.php';
require_once 'classes/subject.class.php';

$subject = new subject($_GET['id']);

$getNotesData = $con->prepare('select id from notes where subjectid = :subjectid');
$getNotesData->bindValue('subjectid', $subject->getData()['id'], PDO::PARAM_INT);
$getNotesData->execute();

$notes = array();

while ($notesData = $getNotesData->fetch()) {
    $note = new note($notesData['id']);
    $notes[] = $note->getData();
}

echo $twig->render('subject.html', array('subjects' => $subjects, 'subject' => $subject->getData(), 'notes' => $notes));
