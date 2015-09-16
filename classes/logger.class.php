<?php

class logger {

  public function __construct() {

  }

  public function log($text) {

    global $con;
    global $config;

    // error_log('Erettsegik event: ' . $text);

    try {

      $query = $con->prepare('insert into happenings values(DEFAULT, :text, now())');
      $query->bindValue('text', $text, PDO::PARAM_STR);
      $query->execute();

    } catch (PDOException $e) {
      die($config['errors']['database'] . $e->getMessage());
    }

  }

}
