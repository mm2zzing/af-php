<?php
namespace apdos\tests\plugins\test;

use apdos\plugins\test\Test_Case;
use apdos\plugins\test\Test_Result;
use apdos\plugins\test\Test_Suite;
use apdos\tests\plugins\test\Was_Run;

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
    $this->assert(0 == strcmp('test_result: 1 run, 0 failed', $result->short_summary()), 'test result summary');
  }

  public function test_mock_object() {
    $test = new Test_Class();
    $this->assert('foo' == $test->foo(), 'original value is foo');
    $this->assert('bar(1)' == $test->bar(1), 'original value is bar(1)');
    $mock = $this->generate_mock('Mock_Test_Class', 
                                 'apdos\tests\plugins\test\Test_Class', 
                                 array('foo', 'bar'), 
                                 array(null, 'param'));
    $mock->set_return('foo', 'mock_foo');
    $mock->set_return('bar', 'mock_bar(2)');
    $this->assert('mock_foo' == $mock->foo(), 'mock value is mock_foo');
    $this->assert('mock_bar(2)' == $mock->bar(1), 'mock value is mock_bar(2)');
  }

  public static function create_suite() {
    $suite = new Test_Suite('Test_Case_Test');
    $suite->add(new Test_Case_Test('test_run'));
    $suite->add(new Test_Case_Test('test_summary'));
    $suite->add(new Test_Case_Test('test_mock_object'));
    return $suite;
  }
}
