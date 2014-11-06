<?php

require_once 'classes/feedback.class.php';

$index_var['location'][] = array('url' => '?p=feedback', 'name' => 'Visszajelzés küldése');

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
        'index_var' => $index_var,
        'status' => $status
    )
);
