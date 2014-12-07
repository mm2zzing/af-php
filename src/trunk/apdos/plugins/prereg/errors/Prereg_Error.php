<?php
namespace apdos\plugins\prereg\errors;

use apdos\kernel\error\apdos_error;

class Prereg_Error extends Apdt_Error {
  public function __construct($message) {
    parent::__construct("Prereg_Error::" . $message);
  }

  public function get_message() {
    $message = $this->getMessage();
    $trace = $this->getTraceAsString();
    return "{$message} $trace";
  }
}

class Prereg_Id_Is_None extends Apdt_Error {
  public function __construct($message) {
    parent::__construct("Prereg_Id_Is_None::" . $message);
  }
}

class Prereg_Uuid_Is_None extends Apdt_Error {
  public function __construct($message) {
    parent::__construct("Prereg_Uuid_Is_None::" . $message);
  }
}

class Prereg_Password_Is_Wrong extends Apdt_Error {
  public function __construct($message) {
    parent::__construct("Prereg_Password_Is_Wrong::" . $message);
  }
}

class Prereg_User_Property_Not_Exist extends Apdt_Error {
  public function __construct($message) {
    parent::__construct("Prereg_User_Property_Not_Exist::" . $message);
  }
}
