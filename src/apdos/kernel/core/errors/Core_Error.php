<?php
namespace apdos\kernel\core\errors;

class Core_Error extends \Exception {
  public function __construct($msg, $code) {
    parent::__construct($msg, $code);
  }
}
