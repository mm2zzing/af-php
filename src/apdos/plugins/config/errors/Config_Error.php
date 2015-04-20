<?php
namespace apdos\plugins\config\errors;

class Config_Error extends \Exception {
  const LOAD_FAILED = 1;

  public function __construct($message, $code) {
    parent::__construct('Config_Error::' . $message, $code);
  }

}
