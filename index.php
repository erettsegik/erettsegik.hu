<?php

ob_start('ob_gzhandler');

session_start();

require_once 'inc/functions.php';
require_once 'vendor/autoload.php';

Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);

use Aptoma\Twig\Extension\MarkdownExtension;
use Aptoma\Twig\Extension\MarkdownEngine;

$engine = new MarkdownEngine\MichelfMarkdownEngine();

$twig->addExtension(new MarkdownExtension($engine));

$getSubjects = $con->query('select * from subjects');

$subjects = array();

while ($subject = $getSubjects->fetch()) {

    $subjects[] = $subject;

}

if (isset($_GET['p'])) {

    $p = $_GET['p'];

    if (file_exists('inc/' . $p . '.php') && $p != 'functions') {

        require_once 'inc/' . $p . '.php';

    } else {

        echo '404';

    }

} else {

    require_once 'inc/welcome.php';

}
