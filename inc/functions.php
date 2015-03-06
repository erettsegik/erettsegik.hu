<?php

$config_file = str_repeat('../', $dir_level) . 'config.php';

require_once str_repeat('../', $dir_level) . 'classes/user.class.php';

if (file_exists($config_file)) {

  require_once $config_file;

} else {

  $url = parse_url(getenv('CLEARDB_DATABASE_URL'));

  $config['db'] = array(
    'host'     => $url['host'],
    'username' => $url['user'],
    'password' => $url['pass'],
    'dbname'   => substr($url['path'], 1),
    'charset'  => 'utf8'
  );

}

$config['clearance'] = array(
  'categories'    => 2,
  'events'        => 2,
  'feedback'      => 2,
  'modifications' => 1,
  'news'          => 2,
  'notes'         => 1,
  'subjects'      => 2,
  'users'         => 4
);

$config['dateformat'] = 'Y-m-d H:i';
$config['htmldate'] = 'Y-m-d\TH:i';

$config['tz']['utc'] = new DateTimeZone('UTC');
$config['tz']['local'] = new DateTimeZone('Europe/Budapest');

$config['errors']['database'] = '
    Hiba történt az adatbázissal való kommunikáció során.
    A jegyzetek biztonsági másolata megtekinthető a
    http://github.com/erettsegik/notes-backup címen.
    Ha a probléma nem oldódik meg pár percen belül, kérlek jelezd nekünk
    e-mailben vagy facebookon.';

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
  die($config['errors']['database']);
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

  if (!isset($_SESSION['userid'])) {
    header('Location: /user_manage/');
    die('Kérlek jelentkezz be.');
  }

  $user = new user($_SESSION['userid']);

  if ($user->getData()['authority'] < $clearance_level) {
    die('Nincs jogod az oldal megtekintéséhez!');
  }

}

function isNotEmpty($text) {

  return trim($text) != '';

}

function isValid($type, $id) {

  global $con;
  global $config;

  switch ($type) {
    case 'category':     $table = 'categories';    break;
    case 'modification': $table = 'modifications'; break;
    case 'news':         $table = 'news';          break;
    case 'note':         $table = 'notes';         break;
    case 'subject':      $table = 'subjects';      break;
    case 'user':         $table = 'users';         break;
    default:             return 0;                 break;
  }

  try {

    $query = $con->prepare('select id from ' . $table . ' where id = :id');
    $query->bindValue('id', $id, PDO::PARAM_INT);
    $query->execute();

  } catch (PDOException $e) {
    die($config['errors']['database']);
  }

  return $query->rowCount();

}

function prepareText($text) {

  $text = trim($text);
  return $text;

}

function unprepareText($text) {

  return htmlspecialchars_decode($text);

}

function getAdminMenuItems() {

  global $config;

  return array(
    array('url' => 'events_admin.php', 'name' => 'Események', 'clearance' => $config['clearance']['events']),
    array('url' => 'users_admin.php', 'name' => 'Felhasználók', 'clearance' => $config['clearance']['users']),
    array('url' => 'news_admin.php', 'name' => 'Hírek', 'clearance' => $config['clearance']['news']),
    array('url' => 'modifications_admin.php', 'name' => 'Javaslatok', 'clearance' => $config['clearance']['modifications']),
    array('url' => 'notes_admin.php', 'name' => 'Jegyzetek', 'clearance' => $config['clearance']['notes']),
    array('url' => 'categories_admin.php', 'name' => 'Kategóriák', 'clearance' => $config['clearance']['categories']),
    array('url' => 'subjects_admin.php', 'name' => 'Tárgyak', 'clearance' => $config['clearance']['subjects']),
    array('url' => 'feedback_admin.php', 'name' => 'Visszajelzések', 'clearance' => $config['clearance']['feedback'])
  );

}

function getDateText($dto) {

  global $config;

  $current = new DateTime(null, $config['tz']['utc']);
  $current->setTimeZone($config['tz']['local']);

  $diff = $current->getTimestamp() - $dto->getTimestamp();

  if ($diff >= 0) {

    if ($diff < 10) {
      return 'épp most';
    } else if ($diff < 60) {
      return $diff . ' másodperce';
    } else if ($diff < 120) {
      return 'egy perce';
    } else if ($diff < 60*60) {
      return (int)($diff/60) . ' perce';
    } else if ($diff < 60*60*2) {
      return 'egy órája';
    } else if ($diff < 60*60*24) {
      return (int)($diff/(60*60)) . ' órája';
    } else if ($diff < 2*60*60*24) {
      return 'egy napja';
    } else {
      return (int)($diff/(60*60*24)) . ' napja';
    }

  } else {

    $diff = abs($diff);

    if ($diff < 10) {
      return 'épp most';
    } else if ($diff < 60) {
      return 'még ' . $diff . ' másodperc';
    } else if ($diff < 120) {
      return 'még egy perc';
    } else if ($diff < 60*60) {
      return 'még ' . (int)($diff/60) . ' perc';
    } else if ($diff < 60*60*2) {
      return 'még egy óra';
    } else if ($diff < 60*60*24) {
      return 'még ' . (int)($diff/(60*60)) . ' óra';
    } else if ($diff < 2*60*60*24) {
      return 'még egy nap';
    } else {
      return 'még ' . (int)($diff/(60*60*24)) . ' nap';
    }

  }

}
