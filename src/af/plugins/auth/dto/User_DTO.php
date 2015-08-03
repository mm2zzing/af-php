<?php
namespace af\plugins\auth\dto;

class User_DTO {
  // 유니크 유저 아이디. 
  public $id = '';
  public $register_id = '';
  public $register_password = '';
  public $register_email = '';
  public $install_ip = '';
  public $install_date = ''; 
  // 서버에서 생성한 인증 토큰. 인증 성공시에 해당 값을 클라이언트에게 전달. 
  // 클라이언트는 이 값을 내부에 저장하여 가지고 있게 된다.
  public $token = '';
  public $unregistered = false;

  // 외부 시스템과 연동시 사용하는 유저 아이디들
  public $external_ids = array(
    'device_id'=>''
  );

  public function is_null() {
    return false;
  }

  public function deserialize($array) {
    foreach ($array as $key=>$value) {
      $this->{$key} = $value;
    }
  }
}
