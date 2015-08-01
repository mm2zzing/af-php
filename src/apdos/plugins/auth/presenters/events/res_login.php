<?php
namespace apdos\plugins\auth\presenters\events;

Loader::get_instance()->include_module('apdos/kernel/actor/events/remote_event');

class Res_Login extends Remote_Event {
  public static $RES_LOGIN = "res_login";

  public function __construct($args) {
    parent::__construct($args, array('construct1'));
  }

  public function construct1($user) {
    $this->set_name(self::$RES_LOGIN);
    $this->set_data(array('user'=>$user));
  }
}
