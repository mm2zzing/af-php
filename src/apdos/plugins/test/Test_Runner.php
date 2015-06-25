<?php
namespace apdos\plugins\test;

use apdos\kernel\log\Logger;

class Test_Runner {
  private $test_suites;
  private $result;
  private $short_result;

  public function __construct() {
    $this->test_suite = array();
    $this->result  = '';
    $this->short_result = '';
  }

  public function add($test_suite) {
    array_push($this->test_suite, $test_suite);
  }

  public function run() {
    foreach($this->test_suite as $test_suite) {
      $test_suite->run();
      $this->result .= ($test_suite->summary() . PHP_EOL);
      $this->short_result .= ($test_suite->short_summary() . PHP_EOL);
    }
  }

  public function short_summary() {
    return trim($this->short_result, PHP_EOL);
  }

  public function summary() {
    return trim($this->result, PHP_EOL);
  }
}

