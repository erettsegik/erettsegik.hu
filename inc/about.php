<?php

$index_var['location'][] = array('url' => '/about/', 'name' => 'Rólunk');

$index_var['title'] = 'Rólunk';

$rendertarget = $_SESSION['mobile'] ? 'mobile/about.html' : 'about.html';

echo $twig->render($rendertarget, array('index_var' => $index_var));
