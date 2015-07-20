<?php
namespace apdos\kernel\objectid;

use apdos\kernel\env\Environment;

abstract class ID {
  const ULONG_4BYTE_LE = "V";
  const USHORT_2BYTE_LE = "v";

  /** 
   * 헥스 스트링을 통한 객체 초기화 
   *
   * @hex string 헥스 스트링 데이터
   */
  public function init_by_string($hex) {
    $this->binary = hex2bin($hex);
  }

  /**
   * 바이너리 데이터를 통한 객체 초기화
   *
   * @data string 바이너리 데이터
   */
  public function init_by_binary($data) {
    $this->binary = $data;
  }

  public function to_string() {
    return bin2hex($this->binary);
  } 

  /**
   * 3바이트로 해시된 머신값을 돌려준다.
   * 
   * 클라우드 서비스의 hostname은 public ip address기반으로 유니크하게 정해져있이므로
   * 머신 아이디로 사용하기에 충분한다.
   * 설사 hostname이 동일하더라도 process_id로 인해 동일한 id값이 생성되는 일은 거의 없다.
   *
   * 다음은 Ruby의 mongodb driver의 구현법과 동일한다.
   *
   * @return string 해시된 3바이트 문자열
   */
  protected function create_hashed_machine_name($hash_size) {
    $result = Environment::get_instance()->get_host_name();
    return substr(md5($result), 0, $hash_size);
  }

  protected function create_process_id() {
    return Environment::get_instance()->get_process_id();
  }

  protected $binary;
}

