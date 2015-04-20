<?php
namespace apdos\plugins\auth\errors;

use apdos\kernel\error\Apdos_Error;

class Auth_Password_Is_Wrong extends Apdos_Error {
  public function __construct($message) {
    parent::__construct("Auth_Password_Is_Wrong::" . $message);
  }
}
