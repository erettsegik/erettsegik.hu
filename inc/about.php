<?php

$index_var['location'][] = array('url' => '/about/', 'name' => 'Rólunk');

$index_var['title'] = 'Rólunk';

echo $twig->render('about.html', array('index_var' => $index_var));
