<?php
namespace af\kernel\user\errors;

use af\kernel\error\Apdos_Error;

class Password_Invalid_Error extends Apdos_Error {
  public function __construct($message) {
    parent::__construct('Password_Invalid_Error::' . $message);
  }
}
