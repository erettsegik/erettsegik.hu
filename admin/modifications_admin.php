<?php

session_start();

$dir_level = 1;

require_once '../inc/functions.php';
require_once '../classes/modification.class.php';

$twig = initTwig();

echo $twig->render('admin/modifications_admin.html');
