<?php
namespace af\plugins\test;

class Test_Result {
  private $test_case_name;
  private $run_count;
  private $failed_count;
  private $failed_messages;


  public function __construct($test_case_name) {
    $this->test_case_name = $test_case_name;
    $this->run_count = 0;
    $this->failed_count = 0;
    $this->failed_messages = array();
  }

  public function add_run_count() {
    $this->run_count += 1;
  }

  public function add_failed($method_name, $message) {
    $this->failed_count += 1;
    $this->failed_messages[$method_name] = $message;
  }

  public function summary() {
    $result = $this->test_case_name. ': '.  $this->run_count.' run, '.$this->failed_count.' failed';
    if ($this->failed_count > 0) {
      $result .= PHP_EOL;
      $result .= $this->get_failed_message();
    }
    return $result;
  }

  private function get_failed_message() {
    $result = '';
    foreach ($this->failed_messages as $method=>$message) {
      $result .= (">Method: $method". PHP_EOL . $message . PHP_EOL);
    }
    return $result;
  }

  public function short_summary() {
    $result = $this->test_case_name. ': '.  $this->run_count.' run, '.$this->failed_count.' failed';
    if ($this->failed_count > 0) {
      $result .= PHP_EOL;
      $result .= $this->get_short_failed_message();
    }
    return $result;
  }

  private function get_short_failed_message() {
    $result = '';
    foreach ($this->failed_messages as $method=>$message) {
      $result .= (">Method: $method". PHP_EOL);
    }
    return $result;
  }
}
?>
