<?php
Loader::get_instance()->include_module('kernel/event/event');

class Req_Register extends Remote_Event {
  public static $REQ_REGISTER = "req_register";

  public function init() {
    parent::init(self::$REQ_REGISTER);
  }

  public function get_register_id() {
    return $this->data['register_id'];
  }

  public function get_register_password() {
    return $this->data['register_password'];
  }

  public function get_register_email() {
    return $this->data['register_email'];
  }
}
