<?php
require_once 'apdos/kernel/error/apdos_error.php';

class Event_Error extends Apdt_Error {
  public function __construct($message) {
    parent::__construct('Event_Error::' . $message);
  }
}
