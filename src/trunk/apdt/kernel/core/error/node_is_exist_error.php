<?php
require_once 'apdt/kernel/error/apdt_error.php';

class Node_Is_Exist_Error extends Apdt_Error {
  public function __construct($message) {
    parent::__construct('Node_Is_Exist_Error::' . $message);
  }
}
