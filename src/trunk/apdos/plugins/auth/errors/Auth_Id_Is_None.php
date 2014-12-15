<?php
namespace apdos\plugins\auth\errors;

use apdos\kernel\error\apdos_error;

class Auth_Id_Is_None extends Apdos_Error {
  public function __construct($message) {
    parent::__construct("Auth_Id_Is_None::" . $message);
  }
}
