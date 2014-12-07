<?php
namespace apdos\plugins\auth\presenters\events;

\Loader::get_instance()->include_module('apdos/kernel/event/event');

class Req_Register_Device extends \Remote_Event {
  public static $REQ_REGISTER_DEVICE = "req_register_device";

  public function init($device_id) {
    parent::init(self::$REQ_REGISTER_DEVICE);
    $this->set_data(array("device_id"=>$device_id));
  }

  public function get_device_id() {
    return $this->data['device_id'];
  }
}
