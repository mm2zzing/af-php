<?php
namespace af\kernel\actor\property\errors;

use af\kernel\error\Apdos_Error;

class Property_Error extends Apdos_Error {
  const PROPERTY_IS_EMPTY = 1;

  public function __construct($property, $code) {
    $name = $property->get_name();
    parent::__construct('Property_Error::name is ' . $name, $code);
  }
}
