<?php
require_once 'apdt/plugins/test/test_case.php';
require_once 'apdt/tests/plugins/test/was_run.php';

class Test_Case_Test extends Test_Case {
  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function test_run() {
    $result = new Test_Result('test_result');

    $was_run = new Was_Run('test_run');
    $was_run->run($result);
    $this->assert(0 == strcmp('set_up test_run tear_down ', $was_run->log), 'test case run');
  }

  public function test_summary() {
    $result = new Test_Result('test_result');
    $was_run = new Was_Run('test_run');

    $was_run->run($result);
    $this->assert(0 == strcmp('test_result 1 run, 0 failed', $result->summary()), 'test result summary');
  }
}
