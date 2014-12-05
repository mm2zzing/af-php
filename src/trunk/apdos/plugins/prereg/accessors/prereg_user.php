<?php
require_once 'apdos/plugins/prereg/dto/prereg_user_dto.php';

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

class Null_Prereg_User extends Prereg_User {
  public function __construct() {
    parent::__construct(new Null_Prereg_User_DTO());
  }

  public function is_null() {
    return true;
  }
}
