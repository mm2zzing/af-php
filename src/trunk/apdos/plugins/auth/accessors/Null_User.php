<?php
namespace apdos\plugins\auth\accessors;

use apdos\plugins\auth\dto\Null_User_DTO;

class Null_User extends User {
  public function __construct() {
    parent::__construct(new Null_User_DTO());
  }

  public function is_null() {
    return true;
  }
}
