<?php

$index_var['location'][] = array('url' => '/404/', 'name' => '404-es hiba');

$index_var['title'] = '404-es hiba';

echo $twig->render('404.html', array('index_var' => $index_var));
