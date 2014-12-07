<?php
namespace apdos\kernel\actor\errors;

use apdos\kernel\error\apdos_error;

class Actor_Error extends Apdos_Error {
  public function __construct($message) {
    parent::__construct('Actor_Error::' . $message);
  }
}
