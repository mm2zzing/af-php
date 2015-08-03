<?php
namespace af\tests\plugins\test;

use af\plugins\test\Test_Case;
use af\plugins\test\Test_Suite;

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

  public static function create_suite() {
    $result = new Test_Suite('Was_Run');
    $result->add(new Was_Run('test_run'));
    return $result;
  }
}
