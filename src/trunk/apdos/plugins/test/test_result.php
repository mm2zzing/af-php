<?php
namespace apdos\plugins\test;

class Test_Result {
  private $test_case_name;
  private $run_count;
  private $failed_count;


  public function __construct($test_case_name) {
    $this->test_case_name = $test_case_name;
    $this->run_count = 0;
    $this->failed_count = 0;
  }

  public function add_run_count() {
    $this->run_count += 1;
  }

  public function add_failed_count() {
    $this->failed_count += 1;
  }

  public function summary() {
    return $this->test_case_name.' '.
            $this->run_count.' run, '.$this->failed_count.' failed';
  }
}
?>
