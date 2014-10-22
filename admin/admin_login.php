<?php

session_start();

$dir_level = 1;

require_once '../inc/functions.php';
require_once '../classes/user.class.php';

$twig = initTwig();

if (!checkRights(0)) {

    $status = 'form';

    if (isset($_POST['submit'])) {

        $user = new user();
        $status = ($user->login($_POST['name'], $_POST['password'])) ? 'success' : 'error';

    }

} else {

    if (isset($_GET['action']) && $_GET['action'] == 'logout') {

        $status = 'logout';
        session_destroy();

    } else {

       $status = 'loggedin';

    }

}

echo $twig->render('admin/admin_login.html', array('status' => $status));
