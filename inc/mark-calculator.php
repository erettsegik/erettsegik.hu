<?php

$index_var['location'][] = array('url' => '/mark-calculator/', 'name' => 'Jegyszámító');

$index_var['title'] = 'Jegyszámító';

if (isset($_POST['save'])) {

  try {

    $getCalculatorData = $con->prepare('
      select id
      from calculatordata
      where userid = :userid
    ');
    $getCalculatorData->bindValue('userid', $_SESSION['userid'], PDO::PARAM_INT);
    $getCalculatorData->execute();

  } catch (PDOException $e) {
    die($config['errors']['database']);
  }

  if ($getCalculatorData->rowCount() == 1) {

    try {

      $setCalculatorData = $con->prepare('
        update calculatordata
        set data = :data, weight = :weight
        where userid = :userid
      ');
      $setCalculatorData->bindValue('userid', $_SESSION['userid'], PDO::PARAM_INT);
      $setCalculatorData->bindValue('data', $_POST['exportstring'], PDO::PARAM_STR);
      $setCalculatorData->bindValue('weight', $_POST['exportweight'], PDO::PARAM_STR);
      $setCalculatorData->execute();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

  } else {

    try {

      $setCalculatorData = $con->prepare('
        insert into calculatordata
        values(DEFAULT, :userid, :data, :weight)
      ');
      $setCalculatorData->bindValue('userid', $_SESSION['userid'], PDO::PARAM_INT);
      $setCalculatorData->bindValue('data', $_POST['exportstring'], PDO::PARAM_STR);
      $setCalculatorData->bindValue('weight', $_POST['exportweight'], PDO::PARAM_STR);
      $setCalculatorData->execute();

    } catch (PDOException $e) {
      die($config['errors']['database']);
    }

  }

}

try {

  $getCalculatorData = $con->prepare('
    select data, weight
    from calculatordata
    where userid = :userid
  ');
  $getCalculatorData->bindValue('userid', $_SESSION['userid'], PDO::PARAM_INT);
  $getCalculatorData->execute();

} catch (PDOException $e) {
  die($config['errors']['database']);
}

if ($getCalculatorData->rowCount() == 1) {
  $data = $getCalculatorData->fetch();
}

echo $twig->render('mark-calculator.html', array('index_var' => $index_var, 'data' => isset($data) ? $data : null));
