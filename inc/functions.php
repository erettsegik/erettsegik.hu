<?php

$config_file = 'config.php';

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

    die('Could not connect to the database.');

}
