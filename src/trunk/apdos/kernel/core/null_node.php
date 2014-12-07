<?php
namespace apdos\kernel\core;

use apdos\kernel\core\node;

class Null_Node extends Node {
  public function __construct() {
    parent::__construct('null', '/null');
  }

  public function is_null() {
    return true;
  }
}
