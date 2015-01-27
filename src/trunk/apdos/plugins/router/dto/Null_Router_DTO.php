<?php
namespace apdos\plugins\router\dto;

class Null_Router_DTO extends Router_DTO {
  public function __construct() {
  }

  public function is_null() {
    return true;
  }
}
