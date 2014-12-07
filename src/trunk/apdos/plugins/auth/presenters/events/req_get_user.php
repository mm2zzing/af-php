<?php
namespace apdos\plugins\auth\presenters\events;

\Loader::get_instance()->include_module('apdos/kernel/actor/events/remote_event');

class Req_Get_User extends \Remote_Event {
  public static $REQ_GET_USER = "req_get_user";

  public function init_by_device_id($device_id) {
    parent::init(self::$REQ_GET_USER);
    $this->set_data(array('device_id'=>$device_id));
  }

  public function get_device_id() {
    return $this->data['device_id'];
  }
}
