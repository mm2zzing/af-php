<?php
namespace tests\apdos\plugins\test;


class Test_Runner_Test extends Test_Case {
  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function test_run() {
    $runner = new Test_Runner();
    $runner->add(Was_Run.create_suite());

    $result = new Test_Result('test_result');

    $was_run = new Was_Run('test_run');
    $was_run->run($result);
    $this->assert(0 == strcmp('set_up test_run tear_down ', $was_run->log), 'test case run');
  } 
}
