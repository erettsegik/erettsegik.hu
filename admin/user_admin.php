<?php

session_start();

$dir_level = 1;

require_once '../inc/functions.php';
require_once '../classes/user.class.php';

if (!checkRights(2)) {
    header('Location: /?p=user_manage');
    die('Kerlek jelentkezz be.');
}

if (!checkRights(4)) {
    die('Nem elég nagy a jogosultságod ennek az oldalnak a megtekintéséhez.');
}

$twig = initTwig();

try {

    $getUsers = $con->prepare('select id from users');

} catch (PDOException $e) {
    die('Nem sikerült a felhasználók kiválasztása.');
}

$getUsers->execute();

if (isset($_POST['update'])) {

    while ($userData = $getUsers->fetch()) {

        $id = $userData['id'];

        $user = new user($id);

        $user->modifyData(
            $_POST[$id . 'name'],
            $_POST[$id . 'authority'],
            $_POST[$id . 'password']
        );

    }

}

if (isset($_POST['addnew'])) {

    $user = new user();

    $user->register($_POST['name'], $_POST['authority'], $_POST['password']);

}

$getUsers->execute();

$users = array();

while ($userData = $getUsers->fetch()) {

    $user = new user($userData['id']);
    $users[] = $user->getData();

}

echo $twig->render(
    'admin/user_admin.html',
    array(
        'users' => $users
    )
);
