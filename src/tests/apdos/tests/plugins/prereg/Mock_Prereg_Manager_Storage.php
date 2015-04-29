<?php
namespace apdos\tests\plugins\prereg;

use apdos\plugins\prereg\models\storage\prereg_storage;
use apdos\plugins\prereg\dto\prereg_user_dto;

class Mock_Prereg_Manager_Storage extends Prereg_Storage {

  public function __construct() {
  }

  public function register($user) {
  }

  public function get_prereg_users($wheres) {
    $result = array();
    for ($i = 0; $i < 100; $i++) {
      $user = array();
      $user['id'] = $i + 1;
      $user['email'] = 'test@mail.com';
      $user['phonenumber'] = '';
      $user['device_type'] = '';
      $user['prereg_ip'] = '';
      $user['prereg_data'] = '';
      array_push($result, $user);
    }
    return $result;
  }

  public function update_prereg_user($wheres, $contents) {
  }
}
