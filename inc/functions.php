<?php

$config_file = str_repeat('../', $dir_level) . 'config.php';

require_once str_repeat('../', $dir_level) . 'classes/user.class.php';

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

$config['dateformat'] = 'Y-m-d H:i';

date_default_timezone_set('Europe/Budapest');

try {

    $con = new PDO('mysql:host=' . $config['db']['host'] .
        ';dbname=' . $config['db']['dbname'] .
        ';charset=utf8',
        $config['db']['username'],
        $config['db']['password']
    );
    $con->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $con->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {

    die('Nem sikerült csatlakozni az adatbázishoz.
        A jegyzetek biztonsági másolata megtekinthető a
        http://github.com/erettsegik címen.');

}

function initTwig() {

    global $dir_level;
    require_once str_repeat('../', $dir_level) . 'vendor/autoload.php';

    Twig_Autoloader::register();

    $loader = new Twig_Loader_Filesystem(str_repeat('../', $dir_level) . 'templates');
    $twig = new Twig_Environment($loader);

    return $twig;

}

function checkRights($clearance_level = 0) {

    if (!isset($_SESSION['userid']))
        return false;

    $user = new user($_SESSION['userid']);

    return ($user->getData()['authority'] >= $clearance_level);

}

function isValid($type, $id) {

    global $con;

    switch ($type) {
        case 'category':     $table = 'categories';    break;
        case 'modification': $table = 'modifications'; break;
        case 'news':         $table = 'news';          break;
        case 'note':         $table = 'notes';         break;
        case 'subject':      $table = 'subjects';      break;
        case 'user':         $table = 'users';         break;
        default:             return 0;                 break;
    }

    $query = $con->prepare('select id from ' . $table . ' where id = :id');
    $query->bindValue('id', $id, PDO::PARAM_INT);
    $query->execute();

    return $query->rowCount();

}

function prepareText($text) {

    $text = trim($text);
    $text = htmlspecialchars($text);
    $text = str_replace('&gt;', '>', $text);

    return $text;

}

function unprepareText($text) {

    return htmlspecialchars_decode($text);

}
