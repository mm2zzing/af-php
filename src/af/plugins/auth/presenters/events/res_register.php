<?php
namespace af\plugins\auth\presenters\events;

Loader::get_instance()->include_module('af/kernel/actor/events/remote_event');

class Res_Register extends Remote_Event {
  public static $RES_REGISTER = "res_register";

  public function __construct($args) {
    $this->set_name(self::$RES_REGISTER);
  }
}
