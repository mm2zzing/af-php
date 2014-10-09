<?php
class User_DTO {
  // 시스템 내부에서 사용하는 유니크 유저 아이디.
  public $id = '';
  public $register_id = '';
  public $register_password = '';
  public $register_email = '';
  public $install_ip = '';
  public $install_date = '';
  // 인증용 유니크 유저 아이디. 서버에서 생성한다.
  public $uuid = '';
  // 인증용 유니크 유저 아이디. 해당 유저가 이용하는 기기의 디바이스를 통해 생성된다.
  public $device_id = '';
  public $unregistered = false;

  public function is_null() {
    return false;
  }

  public function deserialize($array) {
    foreach ($array as $key=>$value) {
      $this->{$key} = $value;
    }
  }
}

class Null_User_DTO {
  public function is_null() {
    return true;
  }
}
