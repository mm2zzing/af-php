<?php
namespace apdos\plugins\auth\presenters\events;

\Loader::get_instance()->include_module('apdos/kernel/actor/events/remote_event');

class Res_Register_Device extends \Remote_Event {
  public static $RES_REGISTER_DEVICE = "res_register_device";

  public static $RESULT_SUCCESS = 0;

  public function init($user) {
    parent::init_with_name(self::$RES_REGISTER_DEVICE);
    $code = self::$RESULT_SUCCESS;
    $data = array('code'=>$code, 'user'=>$user);
    $this->set_data(array('result'=>$data));
  }
}
