<?php

class logger {

  public function __construct() {

  }

  public function log($text) {

    error_log('Erettsegik event: ' . $text);

  }

}
