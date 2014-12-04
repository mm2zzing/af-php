<?php
require_once 'apdos/kernel/event/event.php';
require_once 'apdos/kernel/actor/remote_actor.php';

/**
 * @class Remote_Event
 * 
 * @brief 원격으로 연결되어 있는 Actor간의 이벤트 객체. Actor간의 전송되는 이벤트는
 *        이 객체를 상속받는다.
 *
 */
class Remote_Event extends Event {
  private $remote_actor = null;

  /**
   * 이벤트를 Remote actor와 연결한다. Remote_Event를 다른 Actor로 부터 성공적으로 전달받았을 경우 호출된다.
   */
  public function connect($remote_actor) {
    $this->remote_actor = $remote_actor;
  }

  public function get_remote_actor() {
    if (null == $this->remote_actor)
      return new Null_Remote_Actor();
    return $this->remote_actor;
  }
}

