<?php
namespace apdos\plugins\auth\presenters\events;

Loader::get_instance()->include_module('apdos/kernel/actor/events/remote_event');

class Res_Register_Guest extends Remote_Event {
  public static $RES_REGISTER_GUEST = "res_register_guest";

  public function init($user) {
    parent::init(self::$RES_REGISTER_GUEST);
    $this->set_data(array('user'=>$user));
  }
}
