<?php
namespace apdos\plugins\test;
//require_once 'MockObjectGenerator.php';

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

  /*
  public function generateMock($className, $parentClassName, $arrayMethods, $arrayArgs) {
    $gen = new MockObjectGenerator();
    return $gen->getMock($className, $parentClassName, $arrayMethods, $arrayArgs);
  }
  */

  public function run($result) {
    $result->add_run_count();
    $this->set_up();

    try {
      call_user_func(array($this, $this->test_method_name));
    }
    catch (\Exception $e) {
      $result->add_failed_count();
      $message = $e->getMessage();
      $stack = $e->getTraceAsString();
      echo "Exception: $message $stack" . PHP_EOL;
    }

    $this->tear_down();
  }
}

?>
