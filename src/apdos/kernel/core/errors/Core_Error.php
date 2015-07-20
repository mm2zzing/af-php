<?php
namespace apdos\kernel\core\errors;

class Core_Error extends \Exception {
  public function __construct($msg, $code = -1) {
    parent::__construct($msg, $code);
  }
}
