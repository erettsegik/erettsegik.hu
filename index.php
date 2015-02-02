<?php

ob_start('ob_gzhandler');

session_start();

$dir_level = 0;

require_once 'classes/user.class.php';
require_once 'inc/functions.php';
require_once 'vendor/autoload.php';

Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);

use Aptoma\Twig\Extension\MarkdownExtension;
use Aptoma\Twig\Extension\MarkdownEngine;

$engine = new MarkdownEngine\PHPLeagueCommonMarkEngine();

$twig->addExtension(new MarkdownExtension($engine));

$index_var = array();

try {

    $getSubjects = $con->query('select * from subjects where mandatory = 1');

} catch (PDOException $e) {
    die('Nem sikerült a tantárgyak kiválasztása.');
}

$index_var['subjects'] = array();

while ($subject = $getSubjects->fetch()) {

    $index_var['subjects'][] = $subject;

}

$index_var['location'][] = array('url' => '/', 'name' => 'Főoldal');

if (isset($_SESSION['userid'])) {
    $user = new user($_SESSION['userid']);
    $username = $user->getData()['name'];
}

$index_var['username'] = (isset($_SESSION['userid'])) ? $username : '';

$index_var['css'] = '';

if (isset($_GET['p'])) {

    $p = $_GET['p'];

    if (file_exists('css/' . $p . '.css')) {

        $index_var['css'] = $p;

    }

    if (file_exists('inc/' . $p . '.php') && $p != 'functions') {

        require_once 'inc/' . $p . '.php';

    } else {

        echo '404';

    }

} else {

    $index_var['css'] = 'news';

    require_once 'inc/news.php';

}

ob_end_flush();
