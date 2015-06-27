<?php
namespace apdos\kernel\actor\property;

class Root_Property extends Property {
  public function __construct($name, $value) {
    $this->name = $name;
    $this->value = $value;
  }

  public function get_name() {
    return $this->name;
  }

  public function set_value($value) {
    $this->value = $value;
  }

  public function get_value() {
    return $this->value;
  }

  public function is_null() {
    return false;
  }
}

