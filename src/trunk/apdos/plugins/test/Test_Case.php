<?php
namespace apdos\plugins\test;

class Test_Case {
  private $test_method_name;

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
    try {
      $result->add_run_count();
      $this->set_up();
      $this->run_method();
      $this->tear_down();
    }
    catch (\Exception $e) {
      $this->tear_down();
      $result->add_failed_count();
      $message = $e->getMessage();
      $stack = $e->getTraceAsString();
      echo "Exception: $message $stack" . PHP_EOL;
    }
  }

  private function run_method() {
    call_user_func(array($this, $this->test_method_name));
  }
}

?>
