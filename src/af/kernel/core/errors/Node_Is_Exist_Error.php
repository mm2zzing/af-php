<?php
namespace af\kernel\core\errors;

use af\kernel\error\Apdos_Error;

class Node_Is_Exist_Error extends Apdos_Error {
  public function __construct($message) {
    parent::__construct('Node_Is_Exist_Error::' . $message);
  }
}
