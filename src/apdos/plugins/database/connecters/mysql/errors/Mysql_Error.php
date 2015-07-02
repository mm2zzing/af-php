<?php
namespace apdos\plugins\database\connecters\mysql\errors;

class Mysql_Error extends \Exception {
  const CONNECT_FAIL = 1;
  const CONNECTER_IS_NULL = 2;
  const SCHEMA_IS_NULL = 3;
  const SELECT_DATABASE_FAIELD = 4;
  const QUERY_FAILED = 5;

  public function __construct($message, $code) {
    parent::__construct('Mysql_Error::' . $message, $code);
  }
}
