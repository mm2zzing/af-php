<?php
namespace apdos\kernel\user\errors;

use apdos\kernel\error\Apdos_Error;

class Not_Exist_User_Error extends Apdos_Error {
  public function __construct($message) {
    parent::__construct('Not_Exist_User_Error::' . $message);
  }
}
