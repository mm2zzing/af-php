<?php
namespace af\plugins\auth\errors;

use af\kernel\error\Apdos_Error;

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


