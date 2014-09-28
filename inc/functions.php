<?php

$config_file = str_repeat("../", $dir_level) . 'config.php';

if (file_exists($config_file)) {

    require_once $config_file;

} else {

    $url = parse_url(getenv('CLEARDB_DATABASE_URL'));

    $config['db'] = array(
        'host' => $url['host'],
        'username' => $url['user'],
        'password' => $url['pass'],
        'dbname' => substr($url['path'], 1),
        'charset' => 'utf8'
    );

}

try {

    $con = new PDO('mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['dbname'] . ';charset=utf8', $config['db']['username'], $config['db']['password']);
    $con->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $con->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {

    die('Nem sikerült csatlakozni az adatbázishoz. A jegyzetek biztonsági másolata megtekinthető a http://github.com/erettsegik címen.');

}

function initTwig() {

    global $dir_level;

    require_once str_repeat("../", $dir_level) . "vendor/autoload.php";

    Twig_Autoloader::register();

    $loader = new Twig_Loader_Filesystem(str_repeat("../", $dir_level) . "templates");
    $twig = new Twig_Environment($loader);

    return $twig;

}
