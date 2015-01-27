<?php
namespace apdos\kernel\event;

use apdos\kernel\core\Object_Converter;
use apdos\kernel\event\errors\Event_Error;
use apdos\kernel\event\Event_Database;

class Event {
  protected $name;
  protected $data = array();

  public function __construct() {
  }

  public function init_with_name($name) {
    $this->name = $name;
  }

  /**
   * @TODO json_decode된 파라미터를 검사하여 채워 넣는 방식으로 변경.
   */
  public function init_with_data($name, $data) {
    $this->name = $name;
    $this->set_data($data);
  }

  public function get_type() {
    $tokens = array_slice(explode('\\', get_class($this)), -1);
    return $tokens[0];
  }

  public function get_data() {
    return $this->data;
  }

  public function get_name() {
    return $this->name;
  }

  protected function set_data($data) {
    $this->data = Object_Converter::to_array($data);
  }

  /**
   * C#같은 네임스페이스를 지워하는 언어의 이벤트 클래스는
   * 클래스 이름뿐만 아니라 네임스페이스까지 지정해줘야
   * 플랫폼간 시리얼라이징이 가능하다.
   */
  protected function set_name_space($namespace) {
    $this->namespace = $namespace;
  }

  public static function serialize($event) {
    $array = array();
    $array['type'] = $event->get_type();
    $array['name'] = $event->get_name();
    $array['data'] = $event->get_data();
    return json_encode($array);
  }

  public static function deserialize($json_string) {
    $json_data = json_decode($json_string, true);
    switch (json_last_error()) {
      case JSON_ERROR_NONE:
        break;
      case JSON_ERROR_DEPTH:
        throw new Event_Error('Maximum stack depth exceeded');
        break;
      case JSON_ERROR_STATE_MISMATCH:
        throw new Event_Error('Underflow or the modes mismatch');
        break;
      case JSON_ERROR_CTRL_CHAR:
        throw new Event_Error('Unexpected control character found');
        break;
      case JSON_ERROR_SYNTAX:
        throw new Event_Error('Syntax error, malformed JSON');
        break;
      case JSON_ERROR_UTF8:
        throw new Event_Error('Malformed UTF-8 characters, possibly incorrectly encoded');
        break;
      default:
        throw new Event_Error('Unknown error');
        break;
    }
    $event_type = Event_Database::get_instance()->get_class_name($json_data['type']);
    if (!class_exists($event_type))
      throw new \Exception($event_type . ' is not exist');
    $object = new $event_type();
    $object->init_with_data($json_data['name'], $json_data['data']);
    return $object;
  }

  public static function deserialize_by_parameter($event_type, $event_name, $event_data) {
    $event_type = Event_Database::get_instance()->get_class_name($event_type);
    $object = new $event_type();
    $object->init_with_data($event_name, $event_data);
    return $object;
  }
}
