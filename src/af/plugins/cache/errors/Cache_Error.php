<?php
namespace af\plugins\cache\errors;

class Cache_Error extends \Exception {
  const CACHE_VALUE_IS_NULL = 1;
  const CACHE_WRITE_FAILED = 2;

  public function __construct($msg, $code) {
    parent::__construct($msg, $code);
  }
}
