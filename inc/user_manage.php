<?php

require_once 'inc/functions.php';
require_once 'classes/user.class.php';

$index_var['location'][] = array(
    'url' => '?p=user_manage',
    'name' => 'Felhasználói oldal'
);

$index_var['title'] = 'Felhasználói oldal';

if (!checkRights(0)) {

    $status = 'form';

    if (isset($_POST['submit'])) {

        $user = new user();
        $status = ($user->login($_POST['name'], $_POST['password']))
            ? 'success'
            : 'error';

        if ($status == 'success') {
            header('Location: /?p=user_manage');
        }

    }

} else {

    if (isset($_GET['action']) && $_GET['action'] == 'logout') {

        $status = 'logout';
        session_destroy();
        header('Location: /?p=user_manage');

    } else {

       $status = 'loggedin';

    }

    if (isset($_GET['action']) && $_GET['action'] == 'change_password') {

        $status = 'change_password';

        if (isset($_POST['submit'])) {

            $user = new user($_SESSION['userid']);
            $user->changePassword($_POST['old_password'], $_POST['new_password']);
            header('Location: /?p=user_manage');

        }

    }

}

echo $twig->render(
    'user_manage.html',
    array('index_var' => $index_var,'status' => $status)
);
