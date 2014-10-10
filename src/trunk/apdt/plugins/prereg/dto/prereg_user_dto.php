<?php
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

class Null_Prereg_User_DTO {
  public function is_null() {
    return true;
  }
}
