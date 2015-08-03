<?php
namespace af\kernel\error;

class Apdos_Error extends \Exception {
  public function __construct($msg, $code = -1) {
    parent::__construct($msg, $code);
  } 

  public function get_message() {
    return $this->getMessage();
  }

  public function get_code() {
    return $this->getCode();
  }

  public function get_log() {
    $message = $this->getMessage();
    $trace = $this->getTraceAsString();
    return "{$message} $trace";
  }
}
