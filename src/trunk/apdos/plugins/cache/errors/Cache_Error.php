<?php
namespace apdos\plugins\cache\errors;

class Cache_Error extends \Exception {
  const CACHE_VALUE_IS_NULL = 1;

  public function __construct($msg, $code) {
    parent::__construct($msg, $code);
  }
}
