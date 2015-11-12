<?php

$index_var['location'][] = array('url' => '/mark-calculator/', 'name' => 'Jegyszámító');

$index_var['title'] = 'Jegyszámító';

echo $twig->render('mark-calculator.html', array('index_var' => $index_var));
