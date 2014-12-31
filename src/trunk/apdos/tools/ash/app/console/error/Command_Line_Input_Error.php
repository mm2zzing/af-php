<?php
namespace apdos\tools\ash\app\console\error;

class Command_Line_Input_Error extends \Exception {
  const INPUT_IS_WRONG = 1;

  public function __construct($msg, $code) {
    parent::__construct($msg, $code);
  }
}
