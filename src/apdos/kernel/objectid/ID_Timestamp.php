<?php
namespace apdos\kernel\objectid;

use apdos\kernel\core\Time;
use apdos\kernel\objectid\errors\Backward_Timestamp;
use apdos\kernel\objectid\errors\Increment_Count_Overflow;


class ID_Timestamp {
  const MAX_GENERATE_COUNT_PER_SEC = 65535;
  
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
  public function generate($current_time = -1, $max_generate_count = self::MAX_GENERATE_COUNT_PER_SEC) {
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
    $this->last_timestamp = $current_time;
    return array('gen_timestamp'=>$current_time, 'gen_increment'=>$this->increment);
  }

  public function reset() {
    $this->increment = 0;
    $this->last_timestamp = 0;
  }

  public function get_timestamp() {
    return $this->last_timestamp;
  }

  public function get_increment() {
    return $this->increment;
  }

  private $increment = 0; 
  private $last_timestamp = 0;

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $instance = new ID_Timestamp();
    }
    return $instance;
  }
}
