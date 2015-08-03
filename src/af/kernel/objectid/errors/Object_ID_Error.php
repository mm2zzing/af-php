<?php
namespace af\kernel\objectid\errors;

use af\kernel\error\Apdos_Error;

class Object_ID_Error extends Apdos_Error {
  public function __construct($msg, $code) {
    parent::__construct($msg, $code);
  } 
}
