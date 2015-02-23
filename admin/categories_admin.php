<?php

session_start();

$dir_level = 1;

require_once '../inc/functions.php';
require_once '../classes/category.class.php';

checkRights($config['clearance']['categories']);

$user = new user($_SESSION['userid']);

$twig = initTwig();

try {

  $getCategories = $con->prepare('select id from categories');

} catch (PDOException $e) {
  die('Nem sikerült a kategóriák kiválasztása.');
}

$getCategories->execute();

$status = 'none';

if (isset($_POST['update'])) {

  while ($categoryData = $getCategories->fetch()) {

    $id = $categoryData['id'];

    $category = new category($id);

    $category->modifyData(prepareText($_POST[$id . 'name']));

    $status = 'success';
    $message = 'Sikeresen frissítve!';

  }

}

if (isset($_POST['addnew'])) {

  $category = new category();

  $category->insertData(prepareText($_POST['name']));

  $status = 'success';
  $message = 'Sikeresen frissítve!';

}

$getCategories->execute();

$categories = array();

while ($categoryData = $getCategories->fetch()) {

  $category = new category($categoryData['id']);
  $categories[] = $category->getData();

}

echo $twig->render(
  'admin/categories_admin.html',
  array(
    'categories' => $categories,
    'status'     => $status,
    'message'    => isset($message) ? $message : null,
    'index_var'  => array(
      'menu'           => getAdminMenuItems(),
      'user_authority' => $user->getData()['authority']
    )
  )
);
