<?php
namespace apdos\plugins\auth\errors;

use apdos\kernel\error\apdos_error;

class Auth_Error extends Apdos_Error {
  public function __construct($message) {
    parent::__construct("Auth_Error::" . $message);
  }

  public function get_message() {
    $message = $this->getMessage();
    $trace = $this->getTraceAsString();
    return "{$message} $trace";
  }
}


