<?php
namespace af\plugins\router\dto;

class Config_DTO {
  private $id;
  private $local;
  private $timezone;
  private $maintenance;
  private $log_enables;
  private $error_reporting;

  public function __construct($parsed_data) {
    $this->error_reporting = false;
  }

  public function is_error_reporting() {
    return $this->error_reporting;
  }

  public function is_null() {
    return false;
  }

}
