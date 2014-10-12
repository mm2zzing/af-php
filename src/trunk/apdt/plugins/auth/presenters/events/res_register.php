<?php
Loader::get_instance()->include_module('kernel/actor/events/remote_event');

class Res_Register extends Remote_Event {
  public static $RES_REGISTER = "res_register";

  public function init() {
    parent::init(self::$RES_REGISTER);
  }
}
