<?php
namespace af\kernel\event\errors;

use af\kernel\error\Apdos_Error;

class Event_Error extends Apdos_Error {
  public function __construct($message) {
    parent::__construct('Event_Error::' . $message);
  }
}
