<?php
require_once 'apdos/kernel/error/apdos_error.php';

class Actor_Error extends Apdt_Error {
  public function __construct($message) {
    parent::__construct('Actor_Error::' . $message);
  }
}
