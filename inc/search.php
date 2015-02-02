<?php

$index_var['location'][] = array('url' => '/search/', 'name' => 'Keresés');

$index_var['title'] = 'Keresés';

echo $twig->render('search.html');
