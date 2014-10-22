<?php

session_start();

$dir_level = 1;

require_once '../inc/functions.php';
require_once '../classes/category.class.php';

if (!checkRights(2)) {
    header('Location: admin_login.php');
    die('Kerlek jelentkezz be.');
}

$twig = initTwig();

try {

    $getCategories = $con->prepare('select id from categories');

} catch (PDOException $e) {
    die('Nem sikerült a kategóriák kiválasztása.');
}

$getCategories->execute();

if (isset($_POST['update'])) {

    while ($categoryData = $getCategories->fetch()) {

        $id = $categoryData['id'];

        $category = new category($id);

        $category->modifyData($_POST[$id . 'name']);

    }

}

if (isset($_POST['addnew'])) {

    $category = new category();

    $category->insertData($_POST['name']);

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
        'categories' => $categories
    )
);
