<?php
namespace apdos\plugins\test;

use apdos\kernel\log\Logger;

class Test_Case {
  private $test_method_name;
  private $result;

  public function __construct($test_method_name) {
    $this->test_method_name = $test_method_name;
  }

  public function set_up() {
  }

  public function tear_down() {
  }

  protected function assert($expression, $msg) {
    if (!$expression) {
      throw new \Exception('test failed: ' . $msg);
    }
  }

  public function run($result) {
    $this->result = $result;
    $result->add_run_count();
    $this->set_up();
    $this->run_method();
    $this->tear_down();
  }

  private function run_method() {
    try {
      call_user_func(array($this, $this->test_method_name));
    }
    catch (\Exception $e) {
      $this->result->add_failed_count();
      $message = $e->getMessage();
      $stack = $e->getTraceAsString();
      Logger::get_instance()->error('TEST_CASE', "Exception: $message $stack");
    }
  }
}

?>
