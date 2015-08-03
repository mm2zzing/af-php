<?php
namespace af\plugins\router\dto;

class Null_Register_Get_DTO extends Register_Get_DTO {
  public function __construct() {
  }

  public function is_null() {
    return true;
  }
}

