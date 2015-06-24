<?php
namespace tests\apdos\plugins\prereg;

use apdos\plugins\prereg\models\storage\prereg_storage;
use apdos\plugins\prereg\dto\prereg_user_dto;

class Mock_Prereg_Storage extends Prereg_Storage {
  private $stub_user;

  public function __construct() {
    $this->stub_user = null;
  }

  public function register($user) {
    $this->stub_user = $user;
  }

  public function get_prereg_users($wheres) {
    if (null == $this->stub_user)
      return array();
    else
      return array($this->stub_user);
  }

  public function update_prereg_user($wheres, $contents) {
    foreach ($contents as $key=>$value) {
      $this->stub_user->{$key} = $value;
    }
  }
}
