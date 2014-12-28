<?php
namespace apdos\plugins\database\connecters\mysql\errors;

class Mysql_Error extends \Exception {
  const CONNECT_FAIL = 1;

  public function __construct($message, $code) {
    parent::__construct('Mysql_Error::' . $message, $code);
  }
}
