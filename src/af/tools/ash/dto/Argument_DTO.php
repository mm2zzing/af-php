<?php
namespace af\tools\ash\dto;

class Argument_DTO {
  private $argv;

  public function __construct($argv) {
    $this->argv = $argv;
  }

  public function gets() {
    return $this->argv;
  }

  public function get_count() {
    return count($this->argv);
  }
}
