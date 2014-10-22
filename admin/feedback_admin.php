<?php

session_start();

$dir_level = 1;

require_once '../inc/functions.php';
require_once '../classes/feedback.class.php';

if (!checkRights(2)) {
    header('Location: admin_login.php');
    die('Kerlek jelentkezz be.');
}

$twig = initTwig();

if (isset($_GET['id'])) {

    $status = 'one';

    $feedback = new feedback($_GET['id']);
    $feedbackData = $feedback->getData();
    $feedback->makeNotNew();

} else {

    $status = 'all';

    try {

        $getFeedback = $con->prepare('
            select id
            from feedback
            order by id desc
        ');
        $getFeedback->execute();

    } catch (PDOException $e) {
        die('Nem sikerült a visszajelzések kiválasztása.');
    }

    $feedbackArray = array();

    while ($feedbackData = $getFeedback->fetch()) {

        $feedback = new feedback($feedbackData['id']);
        $feedbackArray[] = $feedback->getData();

    }

}

echo $twig->render(
    'admin/feedback_admin.html',
    array(
        'status' => $status,
        'feedbackdata' => (isset($feedbackData)) ? $feedbackData : null,
        'feedbackarray' => (isset($feedbackArray)) ? $feedbackArray : null
    )
);
