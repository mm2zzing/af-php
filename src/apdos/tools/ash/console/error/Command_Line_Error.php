<?php
namespace apdos\tools\ash\console\error;

class Command_Line_Error extends \Exception {
  const INPUT_IS_WRONG = 1;

  public function __construct($msg, $code) {
    parent::__construct($msg, $code);
  }
}