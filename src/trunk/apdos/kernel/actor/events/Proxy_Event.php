<?php
namespace apdos\kernel\actor\events;

use apdos\kernel\event\event;
use apdos\kernel\event\errors\event_error;

/**
 * @class Proxy_Event
 *
 * @brief 원격으로 연결되어 있는 Actor간의 이벤트를 전달하기위한 이벤트 객체
 * @author Lee Hyeon-gi
 */
class Proxy_Event extends Event {
  public static $PROXY_EVENT = 'proxy_event';
  // 이벤트를 전달할 ACTOR가 없는 경우 설정하는 PATH이다. 상대방 Actor_Accepter로 이벤트가 전송된다.
  public static $NULL_PATH = '';

  /**
   * 생성자
   *
   * @remote_event Remote_Event 전달할 이벤트. Remote_Event 객체만 전달할 수 있다.
   * @sender_path String 이벤트를 전달하는 액터의 path
   * @receiver_path String 이벤트를 전달받는 액터의 path
   */
  public function init($remote_event, $sender_path, $receiver_path) {
    parent::init(self::$PROXY_EVENT);
    $this->set_data($this->create_event_data($remote_event, $sender_path, $receiver_path));
    $this->check_properties();
  } 

  public function init_by_null_receiver($remote_event, $sender_path) {
    parent::init(self::$PROXY_EVENT);
    $this->set_data($this->create_event_data($remote_event, $sender_path, self::$NULL_PATH));
    $this->check_properties();
  }

  public function init_with_data($name, $data) {
    parent::init_with_data($name, $data);
    $this->check_properties();
  }

  private function create_event_data($remote_event, $sender_path, $receiver_path) {
    $data = array();
    $data['target_type'] = $remote_event->get_type();
    $data['target_name'] = $remote_event->get_name();
    $data['target_data'] = $remote_event->get_data();
    $data['sender_path'] = $sender_path;
    $data['receiver_path'] = $receiver_path;
    return $data;
  }

  private function check_properties() {
    if (!isset($this->data['receiver_path']))
      throw new Event_Error('sender_path property is not set');
    if (!isset($this->data['receiver_path']))
      throw new Event_Error('receiver_path property is not set');
    if (!isset($this->data['target_type']))
      throw new Event_Error('target_type property is not set');
    if (!isset($this->data['target_name']))
      throw new Event_Error('target_name property is not set');
    if (!isset($this->data['target_data']))
      throw new Event_Error('target_data property is not set');
  }

  public function get_sender_path() {
    return $this->data['sender_path'];
  }

  public function get_receiver_path() {
    return $this->data['receiver_path'];
  }

  public function get_target_type() {
    return $this->data['target_type'];
  }

  public function get_target_name() {
    return $this->data['target_name'];
  }

  public function get_target_data() {
    return $this->data['target_data'];
  }
}
