<?php

$index_var['location'][] = array('url' => '/getting-started/', 'name' => 'Kezdjünk neki!');

$index_var['title'] = 'Kezdjünk neki!';

echo $twig->render('getting-started.html', array('index_var' => $index_var));
