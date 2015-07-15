<?php
namespace apdos\plugins\sharding\errors;

class Sharding_Error extends \Exception {
  const QUERY_FAILED = 1;
  const CONFIG_FAILED = 2;
  const COMPONENT_FAILED = 3;

  public function __construct($message, $code) {
    parent::__construct('Sharding_Error::' . $message, $code);
  }
}
