<?php
Loader::get_instance()->include_module('kernel/actor/events/remote_event');

class Req_Unregister extends Remote_Event {
  public static $REQ_UNREGISTER = "req_unregister";

  public function init() {
    parent::init(self::$REQ_UNREGISTER);
  }
}
