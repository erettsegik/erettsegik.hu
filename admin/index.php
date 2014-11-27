<?php

session_start();

$dir_level = 1;

require_once '../inc/functions.php';

if (!checkRights(2)) {
    header('Location: /?p=user_manage');
    die('Kerlek jelentkezz be.');
}

$twig = initTwig();

echo $twig->render('admin/index.html');
