<?php

$index_var['location'][] = array('url' => '/faq/', 'name' => 'FAQ');

$index_var['title'] = 'FAQ';

echo $twig->render('faq.html', array('index_var' => $index_var));
