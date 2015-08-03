<?php
namespace af\plugins\auth\accessors;

use af\plugins\auth\dto\Null_User_DTO;

class Null_User extends User {
  public function __construct() {
    parent::__construct(new Null_User_DTO());
  }

  public function is_null() {
    return true;
  }
}
