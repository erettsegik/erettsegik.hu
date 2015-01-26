<?php

$index_var['location'][] = array('url' => '?p=search', 'name' => 'Keresés');

$index_var['title'] = 'Keresés';

echo $twig->render('search.html');
