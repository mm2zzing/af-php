<?php
namespace apdos\plugins\auth\presenters\events;

Loader::get_instance()->include_module('apdos/kernel/actor/events/remote_event');

class Req_Login extends Remote_Event {
  public static $REQ_LOGIN = "req_login";

  public function init_with_register($register_id, $register_password) {
    parent::init(self::$REQ_LOGIN);
  }

  public function get_register_id() {
    return $this->data['register_id'];
  }

  public function get_register_password() {
    return $this->data['register_password'];
  }
}
