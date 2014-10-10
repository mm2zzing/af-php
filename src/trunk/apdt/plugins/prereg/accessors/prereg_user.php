<?php
require_once 'apdt/plugins/prereg/dto/prereg_user_dto.php';

class Prereg_User {
  private $pre_user_dto;

  public function __construct($pre_user_dto) {
    $this->pre_user_dto = $pre_user_dto;
  }

  public function get_pre_user_dto() {
    return $this->pre_user_dto;
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
