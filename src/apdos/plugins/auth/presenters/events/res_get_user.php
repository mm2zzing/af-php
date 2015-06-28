<?php 
namespace apdos\plugins\auth\presenters\events;

\Loader::get_instance()->include_module('apdos/kernel/actor/events/remote_event');

class Res_Get_User extends \Remote_Event {
  public static $RES_GET_USER = "res_get_user";

  public static $RESULT_USER_IS_EXIST = 0;
  public static $RESULT_USER_IS_NOT_EXIST = 1;

  public function __construct($args) {
    parent::__construct($args, array('', 'construct2'));
  }

  public function construct2($result_code, $user) {
    $this->set_name(self::$RES_GET_USER);
    $data = array('code'=>$result_code, 'user'=>$user);
    $this->set_data(array('result'=>$data));
  }
}
