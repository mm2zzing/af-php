<?php
Loader::get_instance()->include_module('kernel/actor/events/remote_event');

class Res_Login extends Remote_Event {
  public static $RES_LOGIN = "res_login";

  public function init($user) {
    parent::init(self::$RES_LOGIN);
    $this->set_data(array('user'=>$user));
  }
}
