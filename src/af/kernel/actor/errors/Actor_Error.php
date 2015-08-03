<?php
namespace af\kernel\actor\errors;

use af\kernel\error\Apdos_Error;

class Actor_Error extends Apdos_Error {
  public function __construct($message) {
    parent::__construct('Actor_Error::' . $message);
  }
}
