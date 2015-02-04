<?php
namespace apdos\kernel\user\errors;

use apdos\kernel\error\Apdos_Error;

class Password_Invalid_Error extends Apdos_Error {
  public function __construct($message) {
    parent::__construct('Password_Invalid_Error::' . $message);
  }
}
