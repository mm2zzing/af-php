<?php
namespace apdos\tests\plugins\test;

use apdos\plugins\test\Test_Case;

class Was_Run extends Test_Case {
  public $log = '';

  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function set_up() {
    $this->log .= 'set_up ';
  }

  public function tear_down() {
    $this->log .= 'tear_down ';
  }

  public function test_run() {
    $this->log .= 'test_run ';
  }
}
