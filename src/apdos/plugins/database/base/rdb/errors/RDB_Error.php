<?php
namespace apdos\plugins\database\base\rdb\errors;

class RDB_Error extends \Exception {
  const CONNECT_FAIL = 1;
  const CONNECTER_IS_NULL = 2;
  const SCHEMA_IS_NULL = 3;
  const UTIL_IS_NULL = 4;
  const SELECT_DATABASE_FAIELD = 5;
  const QUERY_FAILED = 6;

  public function __construct($message, $code) {
    parent::__construct('RDB_Error::' . $message, $code);
  }
}
