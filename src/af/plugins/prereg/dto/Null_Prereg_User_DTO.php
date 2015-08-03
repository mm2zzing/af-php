<?php
namespace af\plugins\prereg\dto;

class Null_Prereg_User_DTO extends Prereg_User_DTO {
  public function is_null() {
    return true;
  }
}
