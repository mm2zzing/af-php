<?php
namespace apdos\plugins\auth\presenters\events;

\Loader::get_instance()->include_module('apdos/kernel/actor/events/remote_event');

class Res_Register_Device extends \Remote_Event {
  public static $RES_REGISTER_DEVICE = "res_register_device";

  public static $RESULT_SUCCESS = 0;

  public function __construct($args) {
    parent::__construct($args, array('construct1'));
  }

  public function construct1($user) {
    $this->set_name(self::$RES_REGISTER_DEVICE);
    $code = self::$RESULT_SUCCESS;
    $data = array('code'=>$code, 'user'=>$user);
    $this->set_data(array('result'=>$data));
  }
}
