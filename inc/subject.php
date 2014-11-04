<?php

require_once 'classes/note.class.php';
require_once 'classes/subject.class.php';

if (isset($_GET['id'])) {

    $status = 'one';

    $subject = new subject($_GET['id']);

    $categories = array();

    $getNotesData = $con->prepare('
        select notes.id, notes.title, categories.name
        from notes
        left join categories on notes.category = categories.id
        where notes.subjectid = :subjectid and notes.live = 1
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

} else {

    $status = 'all';

    $allsubjects = array();

    $getSubjectData = $con->query('select id from subjects order by name asc');

    while($subjectData = $getSubjectData->fetch()) {

        $subject = new subject($subjectData['id']);
        $allsubjects[] = $subject->getData();

    }

}

echo $twig->render(
    'subject.html',
    array(
        'subjects' => $subjects,
        'subject' => $subject->getData(),
        'categories' => (isset($categories)) ? $categories :  null,
        'allsubjects' => (isset($allsubjects)) ? $allsubjects : null,
        'status' => $status
    )
);
