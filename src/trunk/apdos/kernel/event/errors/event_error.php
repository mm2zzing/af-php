<?php
require_once 'apdt/kernel/error/apdt_error.php';

class Event_Error extends Apdt_Error {
  public function __construct($message) {
    parent::__construct('Event_Error::' . $message);
  }
}
