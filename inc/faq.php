<?php

$index_var['location'][] = array('url' => '/faq/', 'name' => 'FAQ');

$index_var['title'] = 'FAQ';

$rendertarget = $_SESSION['mobile'] ? 'mobile/faq.html' : 'faq.html';

echo $twig->render($rendertarget, array('index_var' => $index_var));
