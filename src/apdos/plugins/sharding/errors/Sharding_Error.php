<?php
namespace apdos\plugins\sharding\errors;

use apdos\kernel\error\Apdos_Error;

class Sharding_Error extends Apdos_Error {
  const QUERY_FAILED = 1;
  const CONFIG_FAILED = 2;
  const COMPONENT_FAILED = 3;

  public function __construct($message, $code) {
    parent::__construct('Sharding_Error::' . $message, $code);
  }
}
