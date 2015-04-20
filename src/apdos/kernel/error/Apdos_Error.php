<?php
namespace apdos\kernel\error;

class Apdos_Error extends \Exception {
  public function get_log() {
    $message = $this->getMessage();
    $trace = $this->getTraceAsString();
    return "{$message} $trace";
  }
}
