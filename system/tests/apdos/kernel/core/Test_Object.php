<?php
namespace tests\apdos\kernel\core;

use apdos\kernel\core\Object;

class Test_Object extends Object {
  public function __construct($args = array()) {
    parent::__construct($args, array('construct1', 'construct2', '', 'construct4'));
  }

  public function construct1($v1) {
    $this->log .= 'construct1';
  }

  public function construct2($v1, $v2) {
    $this->log .= 'construct2';
  }

  public function construct4($v1, $v2, $v3, $v4) {
    $this->log .= 'construct4';
  }

  public $log;
}
