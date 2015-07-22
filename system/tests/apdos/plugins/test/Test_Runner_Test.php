<?php
namespace tests\apdos\plugins\test;

use apdos\plugins\test\Test_Case;
use apdos\plugins\test\Test_Runner;
use apdos\plugins\test\Test_Suite;
use tests\apdos\plugins\test\Was_Run;

class Test_Runner_Test extends Test_Case {
  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function test_runner() {
    $runner = new Test_Runner();
    $runner->add(Was_Run::create_suite());
    $runner->run();

    $summary = $runner->short_summary();
    $this->assert($summary == 'Was_Run: 1 run, 0 failed', 'test runner summary');
  } 

  public static function create_suite() {
    $suite = new Test_Suite('Test_Runner_Test');
    $suite->add(new Test_Runner_Test('test_runner'));
    return $suite;
  }
}
