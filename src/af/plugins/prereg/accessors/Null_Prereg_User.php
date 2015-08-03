<?php
namespace af\plugins\prereg\accessors;

use af\plugins\prereg\dto\Null_Prereg_User_DTO;

class Null_Prereg_User extends Prereg_User {
  public function __construct() {
    parent::__construct(new Null_Prereg_User_DTO());
  }

  public function is_null() {
    return true;
  }
}
