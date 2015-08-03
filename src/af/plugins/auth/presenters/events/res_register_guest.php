<?php
namespace af\plugins\auth\presenters\events;

Loader::get_instance()->include_module('af/kernel/actor/events/remote_event');

class Res_Register_Guest extends Remote_Event {
  public static $RES_REGISTER_GUEST = "res_register_guest";

  public function __construct($args) {
    parent::__construct($args, array('construct1'));
  }

  public function construct1($user) {
    $this->set_name(self::$RES_REGISTER_GUEST);
    $this->set_data(array('user'=>$user));
  }
}
