<?php

require_once 'classes/feedback.class.php';

if (isset($_POST['submit'])) {

        $status = 'submit';

        $feedback = new feedback();

        $feedback->insertData($_POST['title'], $_POST['text']);

} else {

        $status = 'form';

}

echo $twig->render(
    'feedback.html',
    array(
        'subjects' => $subjects,
        'status' => $status
    )
);
