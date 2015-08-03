<?php
namespace af\tests\plugins\auth;

use af\plugins\auth\models\storage\auth_storage;
use af\plugins\auth\dto\user_dto;

class Mock_Auth_Storage extends Auth_Storage {
  private $stub_user;

  public function __construct() {
    $this->stub_user = null;
  }

  public function register($user) {
    $this->stub_user = $user;
  }

  public function get_users($wheres) {
    if (null == $this->stub_user)
      return array();
    else
      return array($this->stub_user);
  }

  public function update_user($wheres, $contents) {
    foreach ($contents as $key=>$value) {
      $this->stub_user->{$key} = $value;
    }
  }

  public function unregister($user_uuid) {
    if (null == $this->stub_user)
      return;
    $this->stub_user['unregistered'] = true;
  }
}
