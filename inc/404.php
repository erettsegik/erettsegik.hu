<?php

$index_var['location'][] = array('url' => '/404/', 'name' => '404-es hiba');

$index_var['title'] = '404-es hiba';

$rendertarget = $_SESSION['mobile'] ? 'mobile/404.html' : '404.html';

echo $twig->render($rendertarget, array('index_var' => $index_var));
