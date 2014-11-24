<?php

require_once 'inc/functions.php';
require_once 'classes/user.class.php';

$twig = initTwig();

$index_var['location'][] = array('url' => '?p=login', 'name' => 'Bejelentkezés');

if (!checkRights(0)) {

    $status = 'form';

    if (isset($_POST['submit'])) {

        $user = new user();
        $status = ($user->login($_POST['name'], $_POST['password'])) ? 'success' : 'error';

        if ($status == 'success') {
            header("Location: /?p=login");
        }

    }

} else {

    if (isset($_GET['action']) && $_GET['action'] == 'logout') {

        $status = 'logout';
        session_destroy();
        header("Location: /?p=login");

    } else {

       $status = 'loggedin';

    }

}

echo $twig->render('login.html', array('index_var' => $index_var,'status' => $status));
