<?php
namespace apdos\plugins\auth\presenters\events;

Loader::get_instance()->include_module('apdos/kernel/actor/events/remote_event');

class Req_Unregister extends Remote_Event {
  public static $REQ_UNREGISTER = "req_unregister";

  public function init() {
    parent::init_with_name(self::$REQ_UNREGISTER);
  }
}
