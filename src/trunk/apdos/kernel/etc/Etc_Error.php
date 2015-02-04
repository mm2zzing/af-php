<?php
namespace apdos\kernel\etc;

class Etc_Error extends \Exception {
  const LOAD_FAILED = 1;
  const SAVE_FAILED = 2;
  const PUSH_FAILED = 3;

  public function __construct($message, $code) {
    parent::__construct('Etc_Error::' . $message, $code);
  }

}
