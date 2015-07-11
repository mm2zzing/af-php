<?php
namespace apdos\kernel\core\permission;

class Owner {
  public function __construct($name) {
    $this->name = $name;
  }

  public function get_name() {
    return $this->name;
  }

  private $name;
}
