<?php

checkRights($config['clearance']['logs']);

try {

  $getLogs = $con->prepare('
    select text, date
    from happenings
    order by id desc
  ');
  $getLogs->execute();

} catch (PDOException $e) {
  die($config['errors']['database']);
}

$logsArray = array();

while ($logData = $getLogs->fetch()) {

  $logsArray[] = $logData;

}

echo $twig->render(
  'admin/logs_admin.html',
  array(
    'logsarray' => $logsArray,
    'index_var' => $index_var,
  )
);
