<?php
namespace ft\sys\presenters\auth_presenter\events;

\Loader::get_instance()->include_module('kernel/actor/events/remote_event');

class Res_Register_Device extends \Remote_Event {
  public static $RES_REGISTER_DEVICE = "res_register_device";

  public static $RESULT_SUCCESS = 0;

  public function init($user) {
    parent::init(self::$RES_REGISTER_DEVICE);
    $code = self::$RESULT_SUCCESS;
    $data = array('code'=>$code, 'user'=>$user);
    $this->set_data(array('result'=>$data));
  }
}
