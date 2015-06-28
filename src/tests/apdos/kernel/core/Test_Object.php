<?php
namespace tests\apdos\kernel\core;

use apdos\kernel\core\Object;

class Test_Object extends Object {
  public function __construct($args = array()) {
    parent::__construct($args, array('constructor1', 'constructor2', '', 'constructor4'));
  }

  public function constructor1($v1) {
    $this->log .= 'constructor1';
  }

  public function constructor2($v1, $v2) {
    $this->log .= 'constructor2';
  }

  public function constructor4($v1, $v2, $v3, $v4) {
    $this->log .= 'constructor4';
  }

  public $log;
}
