<?php
namespace af\kernel\user\errors;

use af\kernel\error\Apdos_Error;

class Not_Exist_User_Error extends Apdos_Error {
  public function __construct($message) {
    parent::__construct('Not_Exist_User_Error::' . $message);
  }
}
