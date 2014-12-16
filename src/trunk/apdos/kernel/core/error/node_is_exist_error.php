<?php
namespace apdos\kernel\core\error;

use apdos\kernel\error\apdos_error;

class Node_Is_Exist_Error extends Apdt_Error {
  public function __construct($message) {
    parent::__construct('Node_Is_Exist_Error::' . $message);
  }
}
