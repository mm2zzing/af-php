<?php
namespace apdos\kernel\core;

use apdos\kernel\actor\Component;

/**
 * @class Time
 */
class Time extends Component {
  private $start_time = 0;

  /**
   * 타임 컴포넌트에 필요한 정보를 로드
   */
  public function load() {
    $this->start_time = $this->get_timestamp();
  }

  /**
   * 현재 유닉스타임스탬프값을 반환
   *
   * @return float OS시스템에 따라 32/64비트 float형이 결정됨
   */
  public function get_timestamp() {
    return microtime(true);
  }

  /** 
   * 시스템 구동후 지나간 시작 출력
   */
  public function get_passed_timestamp() {
    return $this->get_timestamp() - $this->start_time;
  }

  /**
   * 현재 요일을 반환 
   *
   * @return string
   */
  public function get_day_of_week() {
    $days = array('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat');
    return $days[date('w', microtime(true))];
  }

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $actor = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/sys/time');
      $instance = $actor->add_component('apdos\kernel\core\Time');
    }
    return $instance;
  }
}
