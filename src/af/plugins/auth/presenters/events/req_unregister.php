<?php
namespace af\plugins\auth\presenters\events;

Loader::get_instance()->include_module('af/kernel/actor/events/remote_event');

class Req_Unregister extends Remote_Event {
  public static $REQ_UNREGISTER = "req_unregister";

  public function __construct($args) {
    $this->set_name(self::$REQ_UNREGISTER);
  }
}
