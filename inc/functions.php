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

    $con = new PDO('mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['dbname'] . ';charset=utf8', $config['db']['username'], $config['db']['password']);
    $con->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $con->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {

    die('Nem sikerült csatlakozni az adatbázishoz. A jegyzetek biztonsági másolata megtekinthető a http://github.com/erettsegik címen.');

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

function formattedDiff($original_text, $new_text) {

    $i = 0;
    while ($original_text[$i] == $new_text[$i]) {
        $i++;
    }

    $o_i = strlen($original_text);
    $n_i = strlen($new_text);

    while ($original_text[$o_i] == $new_text[$n_i]) {
        $o_i--;
        $n_i--;
    }

    $temp = $new_text;

    return '<span class="unchanged">' . substr($temp, 0, $i) . '</span>' . substr($temp, $i, $n_i - $i + 1) . '<span class="unchanged">' . substr($temp, $n_i + 1) . '</span>';

}
