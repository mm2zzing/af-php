<?php
namespace apdos\kernel\actor\property;

abstract class Property {
  abstract public function get_name();
  abstract public function set_value($value);
  abstract public function get_value();
  abstract public function is_null();

  protected $name;
  protected $value;
}
