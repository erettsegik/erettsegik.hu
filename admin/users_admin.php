<?php

require_once '../classes/user.class.php';

checkRights($config['clearance']['users']);

try {

  $getUsers = $con->prepare('select id from users');

} catch (PDOException $e) {
  die($config['errors']['database']);
}

$getUsers->execute();

if (isset($_POST['update'])) {

  while ($userData = $getUsers->fetch()) {

    $id = $userData['id'];

    $user = new user($id);

    $user->modifyData(
      $_POST[$id . 'name'],
      $_POST[$id . 'authority'],
      $_POST[$id . 'password']
    );

    $status = 'success';
    $message = 'Sikeres mentés!';

  }

}

if (isset($_POST['addnew'])) {

  $user = new user();

  $user->register($_POST['name'], $_POST['authority'], $_POST['password']);

  $status = 'success';
  $message = 'Sikeres mentés!';

}

$getUsers->execute();

$users = array();

while ($userData = $getUsers->fetch()) {

  $user = new user($userData['id']);
  $users[] = $user->getData();

}

echo $twig->render(
  'admin/users_admin.html',
  array(
    'index_var' => $index_var,
    'users'     => $users,
    'status'    => $status,
    'message'   => $message
  )
);
