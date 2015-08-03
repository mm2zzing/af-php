<?php
namespace af\plugins\prereg\dto;

class Prereg_User_DTO {
  public $id = '';
  public $email = '';
  public $phonenumber = '';
  public $device_type = '';

  public $prereg_ip = '';
  public $prereg_date = '';

  public function is_null() {
    return false;
  }

  public function deserialize($array) {
    foreach ($array as $key=>$value) {
      $this->{$key} = $value;
    }
  }
}
