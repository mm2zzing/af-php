<?php
require_once 'apdt/kernel/error/apdt_error.php';

class Actor_Error extends Apdt_Error {
  public function __construct($message) {
    parent::__construct('Actor_Error::' . $message);
  }
}
