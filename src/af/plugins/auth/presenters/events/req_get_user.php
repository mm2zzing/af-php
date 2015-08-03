<?php
namespace af\plugins\auth\presenters\events;

\Loader::get_instance()->include_module('af/kernel/actor/events/remote_event');

class Req_Get_User extends \Remote_Event {
  public static $REQ_GET_USER = "req_get_user";

  public function __construct($args) {
    parent::__construct($args, array('construct1'));
  }

  public function construct1($device_id) {
    $this->set_name(self::$REQ_GET_USER);
    $this->set_data(array('device_id'=>$device_id));
  }

  public function get_device_id() {
    return $this->data['device_id'];
  }
}
