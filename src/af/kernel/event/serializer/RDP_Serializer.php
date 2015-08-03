<?php 
namespace af\kernel\event\serializer;

use af\kernel\event\Event_Database;
use af\kernel\event\errors\Event_Error;

/**
 * @class RDP_Serializer
 *
 * @brief Json 프로토콜을 사용하는 시리얼라이저. APD/OS 내부에서 사용하는 REDP 프로토콜을 지원하기 위한 객체이다
 *        REDF는 Remote event dispatch format의 약자이다. 
 *        
 * @author Lee, Hyeon-gi
 */
class RDP_Serializer extends Serializer {
  /**
   * 직렬화 
   *
   * @param event Event 이벤트 객체
   * @return string json 문자열
   */
  public function write($event) {
    $array = array();
    $array['type'] = $event->get_type();
    $array['name'] = $event->get_name();
    $array['data'] = $event->get_data();
    return json_encode($array);
  }
}
