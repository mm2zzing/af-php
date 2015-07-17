<?php
namespace apdos\kernel\objectid;

use apdos\kernel\core\Time;
use apdos\kernel\objectid\errors\Backward_Timestamp;
use apdos\kernel\objectid\errors\Increment_Count_Overflow;
use apdos\kernel\env\Environment;


class ID {
  const MAX_GENERATE_COUNT_PER_SEC = 65535;
  const TIMESTAMPE_BYTE = 4;
  const MACHINE_ID_BYTE = 3;
  const PROCESS_ID_BYTE = 2;
  const INCREMENT_COUNT_BYTE = 2;

  const ULONG_4BYTE_LE = "V";
  const USHORT_2BYTE_LE = "v";

  public function __construct() {
  }
 
  /**
   * 머신, 프로세스간 거의 겹치지 않는 11byte 바이너리 데이터를 생성. 
   * 절대 중복되지 않는건 아니지만 Unique value로 쓸만큼 충분한 수준이다. 
   * 
   * Mongodb의 ObjectID도 이와 유사한 방식을 사용한다. 하지만 서버에서 겹치는지 체크를 하므로 안전하다.
   *
   * Timestamp(4byte) + Machine ID(3byte) + Process ID(2byte) + Increment count(2byte)
   *
   * @param current_time int 현재 유닉스타임스탬프
   * @return string 바이너리 문자열
   *
   * @throw Object_ID_Error
   */ 
  public function generate_id($current_time = -1, $max_generate_count = self::MAX_GENERATE_COUNT_PER_SEC) {
    if (-1 == $current_time)
      $current_time = Time::get_instance()->get_timestamp();

    if ($current_time < $this->last_timestamp)
      throw new Backward_Timestamp('current time is little than last time');

    if ($current_time == $this->last_timestamp) {
      if ($this->increment >= $max_generate_count)
        throw new Increment_Count_Overflow('');
    }
    else {
      $this->increment = 0; 
    }
    $this->increment++;
    $count = $this->increment;
    $this->last_timestamp = $current_time;

    $buf = '';
    $buf .= pack(self::ULONG_4BYTE_LE, $current_time);
    $buf .= $this->get_hashed_machine_name();
    $buf .= pack(self::USHORT_2BYTE_LE, Environment::get_instance()->get_process_id());
    $buf .= pack(self::USHORT_2BYTE_LE, $count);
    return $buf;
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
  private function get_hashed_machine_name() {
    $result = Environment::get_instance()->get_host_name();
    return substr(md5($result), 0, self::MACHINE_ID_BYTE);
  }

  public function reset() {
    $this->increment = 0;
    $this->last_timestamp = 0;
  }

  public function get_current_increment() {
    return $this->increment;
  }

  private $increment = 0; 
  private $last_timestamp = 0;

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $instance = new ID();
    }
    return $instance;
  }
}
