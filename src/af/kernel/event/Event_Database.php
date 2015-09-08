<?php
namespace af\kernel\event;

use af\kernel\event\dto\Event_DTO;

/**
 * @class Event_Database
 *
 * @brief 정의된 이벤트 데이터를 관리
 * @author Lee Hyeon-gi
 */
class Event_Database {
  private $class_names = array();

  public function __construct() {
  }

  /**
   * 이벤트 직렬화 역직렬화시에 사용할 이벤트 객체의 정보를 로드한다.
   *
   * @param events array(array()) 이벤트 객체 정보들
   */
  public function load($events) {
    foreach ($events as $event) {
      $dto = new Event_DTO();
      $dto->name = $event->name;
      $dto->class = $event->class;
      $this->add_event($dto->name, $dto->class);
    }
  }

  /**
   * 이벤트 클래스의 이름을 조회
   * 
   * @param event_name String 이벤트 이름
   * @return String 이벤트 클래스 이름(네임스페이스포함)
   */
  public function get_class_name($event_name) {
    if (isset($this->class_names[$event_name]))
      return $this->class_names[$event_name];
    return '';
  }

  public function add_event($event_name, $class_name) {
    $this->class_names[$event_name] = $class_name;
  }

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $instance = new Event_Database();
    }
    return $instance;
  }
}
