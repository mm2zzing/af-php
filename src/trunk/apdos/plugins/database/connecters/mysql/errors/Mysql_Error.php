<?php
namespace apdos\plugins\database\connecters\mysql\errors;

class Mysql_Error extends \Exception {
  const CONNECT_FAIL = 1;
  const CONNECTER_IS_NULL = 2;
  const SELECT_DATABASE_FAIELD = 3;
  const QUERY_FAILED = 4;

  public function __construct($message, $code) {
    parent::__construct('Mysql_Error::' . $message, $code);
  }
}
