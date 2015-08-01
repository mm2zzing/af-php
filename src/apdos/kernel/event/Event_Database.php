<?php
namespace apdos\kernel\event;

/**
 * @class Event_Database
 *
 * @brief 정의된 이벤트 데이터를 관리
 * @author Lee Hyeon-gi
 */
class Event_Database {
  private $class_names = array();

  public function __construct() {
    // @TODO REMOVE
    $this->add_event('Proxy_Event', 'apdos\kernel\actor\events\Proxy_Event');
    $this->add_event('Dummy_Event', 'tests\apdos\kernel\event\Dummy_Event');
    $this->add_event('Req_Get_User', 'apdos\plugins\auth\presenters\events\events\Req_Get_User');
    $this->add_event('Res_Get_User', 'apdos\plugins\auth\presenters\events\events\Res_Get_User');
    $this->add_event('Req_Register_Device', 'apdos\plugins\auth\presenters\events\events\Req_Register_Device');
    $this->add_event('Res_Register_Device', 'apdos\plugins\auth\presenters\events\events\Res_Register_Device');
    $this->add_event('Shell_Command', 'apdos\tools\ash\events\Shell_Command');
  }

  /**
   * 이벤트 직렬화 역직렬화시에 사용할 이벤트 객체의 정보를 로드한다.
   *
   * @param events array(array()) 이벤트 객체 정보들
   */
  public function load($events) {
    // @TODO
    // config/event.json에 정의되어 있는 이벤트 객체 정보를 로드한다. 이벤트 설정 파일을 쉽게 만들 수 있는 툴을 제공한다.
    // 모든 php 소스를 뒤져서 Event 객체를 상속받은 객체를 조회 ? 아니면 이벤트 정의 툴을 만들면 해당툴이 이벤트 클래스와 event.json 
    // 파일을 생성한다. 그리고 apdos-php와 통신해서 자동으로 해당 내용을 반영한다.
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
