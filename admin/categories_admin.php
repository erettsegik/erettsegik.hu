<?php

require_once '../classes/category.class.php';

checkRights($config['clearance']['categories']);

try {

  $getCategories = $con->prepare('select id from categories');

} catch (PDOException $e) {
  die($config['errors']['database']);
}

$getCategories->execute();

$status = 'none';

if (isset($_POST['update'])) {

  while ($categoryData = $getCategories->fetch()) {

    $id = $categoryData['id'];

    $category = new category($id);

    $category->modifyData($_POST[$id . 'name']);

    $status = 'success';
    $message = 'Sikeresen frissítve!';

  }

}

if (isset($_POST['addnew'])) {

  $category = new category();

  $category->insertData($_POST['name']);

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
    'index_var'  => $index_var,
    'message'    => isset($message) ? $message : null,
    'status'     => $status
  )
);
