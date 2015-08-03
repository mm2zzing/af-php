<?php
namespace af\plugins\auth\errors;

use af\kernel\error\Apdos_Error;

class Auth_Is_Unregistered extends Apdos_Error {
  public function __construct($message) {
    parent::__construct("Auth_Is_Unregistered::" . $message);
  }
}

