<?php
namespace apdos\plugins\prereg\accessors;

use apdos\plugins\prereg\dto\Prereg_User_DTO;

class Prereg_User {
  private $prereg_user_dto;

  public function __construct($prereg_user_dto) {
    $this->prereg_user_dto = $prereg_user_dto;
  }

  public function get_prereg_user_dto() {
    return $this->prereg_user_dto;
  }

  public function is_null() {
    return false;
  }
}
