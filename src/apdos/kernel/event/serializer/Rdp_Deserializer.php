<?php 
namespace apdos\kernel\event\serializer;

use apdos\kernel\event\Event_Database;
use apdos\kernel\event\errors\Event_Error;

/**
 * @class Rdp_Deserializer
 *
 * @brief Json 프로토콜을 사용하는 시리얼라이저. APD/OS 내부에서 사용하는 REDP 프로토콜을 지원하기 위한 객체이다
 *        REDF는 Remote event dispatch format의 약자이다. 
 *        
 * @author Lee, Hyeon-gi
 */
class Rdp_Deserializer extends Deserializer {
  /**
   * 역직렬화
   *
   * @param json_string string json 문자열
   * @param event_class_name string 역직렬화할 이벤트 클래스명
   * @return Event 이벤츠 객체
   */
  public function read($json_string) {
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
      throw new \Exception('Not exist event type => ' . $event_type);
    $object = new $event_type();
    $object->init_with_data($json_data['name'], $json_data['data']);
    return $object;
  } 
}
