<?php

require_once 'classes/note.class.php';
require_once 'classes/subject.class.php';

$index_var['location'][] = array('url' => '?p=subject', 'name' => 'Tantárgyak');

if (isset($_GET['id'])) {

    $status = 'one';

    $subject = new subject($_GET['id']);

    $categories = array();

    try {

        $getNotesData = $con->prepare('
            select notes.id, notes.title, categories.name
            from notes
            left join categories on notes.category = categories.id
            where notes.subjectid = :subjectid and notes.live = 1
        ');
        $getNotesData->bindValue('subjectid', $subject->getData()['id'], PDO::PARAM_INT);
        $getNotesData->execute();

    } catch (PDOException $e) {
        die('Nem sikerült a jegyzetek kiválasztása.');
    }

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

    $index_var['location'][] = array('url' => '?p=subject&amp;id=' . $subject->getData()['id'], 'name' => $subject->getData()['name']);

} else {

    $status = 'all';

    $allsubjects = array();

    try {

        $getSubjectData = $con->query('select id from subjects order by name asc');

    } catch (PDOException $e) {
        die('Nem sikerült a tantárgyak kiválasztása.');
    }

    while($subjectData = $getSubjectData->fetch()) {

        $subject = new subject($subjectData['id']);
        $allsubjects[] = $subject->getData();

    }

}

echo $twig->render(
    'subject.html',
    array(
        'index_var' => $index_var,
        'subject' => $subject->getData(),
        'categories' => (isset($categories)) ? $categories :  null,
        'allsubjects' => (isset($allsubjects)) ? $allsubjects : null,
        'status' => $status
    )
);
