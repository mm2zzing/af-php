<?php
namespace apdos\kernel\event\errors;

use apdos\kernel\error\apdos_error;

class Event_Error extends Apdos_Error {
  public function __construct($message) {
    parent::__construct('Event_Error::' . $message);
  }
}
