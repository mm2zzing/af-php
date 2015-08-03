<?php
namespace af\plugins\auth\accessors;

use af\plugins\auth\dto\User_DTO;

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
