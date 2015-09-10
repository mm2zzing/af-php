<?php
namespace af\plugins\sctrl\errors;

use af\kernel\error\Apdos_Error;

class Schema_Error extends Apdos_Error {
  const COMPONENT_NOT_EXIST = 1;

  public function __construct($message, $code) {
    parent::__construct('Schema_Error::' . $message, $code);
  }
}
