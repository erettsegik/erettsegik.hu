<?php

session_start();

$dir_level = 1;

require_once '../inc/functions.php';

$twig = initTwig();

echo $twig->render('admin/index.html');
