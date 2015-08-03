<?php
namespace af\kernel\objectid\errors;

class Backward_Timestamp extends Object_ID_Error {
  public function __construct($msg) {
    parent::__construct($msg, Error_Codes::ERROR_CODE_BACKWARD_TIMESTAMP);
  }
}
