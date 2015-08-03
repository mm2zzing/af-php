<?php
namespace af\kernel\actor\property;

class Null_Property extends Property {
  public function __construct($name) {
    $this->name = $name;
    $this->value = '';
  }

  public function get_name() {
    return $this->name;
  }

  public function set_value($value) {
  }

  public function get_value() {
    return $this->value;
  }

  public function is_null() {
    return true;
  }
}

