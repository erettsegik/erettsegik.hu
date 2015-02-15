<?php

session_start();

$dir_level = 1;

require_once '../inc/functions.php';

checkRights(1);

$twig = initTwig();

$user = new user($_SESSION['userid']);

echo $twig->render('admin/index.html', array('index_var' => array('menu' => getAdminMenuItems(), 'user_authority' => $user->getData()['authority'])));
