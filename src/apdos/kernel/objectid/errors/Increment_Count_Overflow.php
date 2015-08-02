<?php
namespace apdos\kernel\objectid\errors;

class Increment_Count_Overflow extends Object_ID_Error {
  public function __construct($msg) {
    parent::__construct($msg, Error_Codes::ERROR_CODE_INCREMENT_COUNT_OVERFLOW);
  }
}
