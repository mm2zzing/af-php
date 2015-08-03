<?php
namespace af\tools\ash\console\error;

class Command_Line_Error extends \Exception {
  const INPUT_IS_WRONG = 1;
  const OPTION_NAME_IS_WRONG = 2;

  public function __construct($msg, $code) {
    parent::__construct($msg, $code);
  }
}
