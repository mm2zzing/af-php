<?php
namespace apdos\plugins\auth\presenters\events;

Loader::get_instance()->include_module('apdos/kernel/actor/events/remote_event');

class Res_Unregister extends Remote_Event {
  public static $RES_UNREGISTER = "res_unregister";

  public function init() {
    parent::init(self::$RES_UNREGISTER);
  }
}
