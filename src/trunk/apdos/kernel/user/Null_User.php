<?php
namespace apdos\kernel\user;

class Null_User extends User {
  public function __construct() {
    parent::__construct('null', '*');
  }

  public function is_null() {
    return true;
  }
}
