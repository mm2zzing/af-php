<?php
require_once 'apdos/kernel/error/apdos_error.php';

class Auth_Error extends Apdt_Error {
  public function __construct($message) {
    parent::__construct("Auth_Error::" . $message);
  }

  public function get_message() {
    $message = $this->getMessage();
    $trace = $this->getTraceAsString();
    return "{$message} $trace";
  }
}

class Auth_Id_Is_None extends Apdt_Error {
  public function __construct($message) {
    parent::__construct("Auth_Id_Is_None::" . $message);
  }
}

class Auth_Uuid_Is_None extends Apdt_Error {
  public function __construct($message) {
    parent::__construct("Auth_Uuid_Is_None::" . $message);
  }
}

class Auth_Password_Is_Wrong extends Apdt_Error {
  public function __construct($message) {
    parent::__construct("Auth_Password_Is_Wrong::" . $message);
  }
}

class Auth_Is_Unregistered extends Apdt_Error {
  public function __construct($message) {
    parent::__construct("Auth_Is_Unregistered::" . $message);
  }
}
