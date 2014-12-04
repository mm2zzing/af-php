<?php
require_once 'apdos/plugins/auth/dto/user_dto.php';

class User {
  private $user_dto;

  public function __construct($user_dto) {
    $this->user_dto = $user_dto;
  }

  public function get_user_dto() {
    return $this->user_dto;
  }

  public function is_null() {
    return false;
  }
}

class Null_User extends User {
  public function __construct() {
    parent::__construct(new Null_User_DTO());
  }

  public function is_null() {
    return true;
  }
}
