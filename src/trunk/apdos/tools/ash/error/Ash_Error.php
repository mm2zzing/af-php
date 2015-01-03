<?php
namespace apdos\tools\ash\error;

class Ash_Error extends \Exception {
  const UNKNOW_COOMAND = 1;

  public function __construct($msg, $code) {
    parent::__construct($msg, $code);
  }
}
