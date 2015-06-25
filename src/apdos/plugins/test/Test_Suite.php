<?php
namespace apdos\plugins\test;

use apdos\kernel\log\Logger;
use apdos\plugins\test\Test_Result;

class Test_Suite {
  private $suite_name;
  private $test_cases;
  private $test_result;

  public function __construct($suite_name) {
    $this->suite_name = $suite_name;
    $this->test_cases = array();
  }

  public function add($test_case) {
    array_push($this->test_cases, $test_case);
  }

  public function run() {
    $this->test_result = new Test_Result($this->suite_name);
    foreach ($this->test_cases as $test_case) {
      $test_case->run($this->test_result);
    }
  }

  public function short_summary() {
    return $this->test_result->short_summary();
  }

  public function summary() {
    return $this->test_result->summary();
  }
}

