<?php
namespace af\plugins\auth\presenters\events;

\Loader::get_instance()->include_module('af/kernel/event/event');

class Req_Register_Device extends \Remote_Event {
  public static $REQ_REGISTER_DEVICE = "req_register_device";

  public function __construct($args) {
    parent::__construct($args, array('construct1'));
  }

  public function construct1($device_id) {
    $this->set_name(self::$REQ_REGISTER_DEVICE);
    $this->set_data(array("device_id"=>$device_id));
  }

  public function get_device_id() {
    return $this->data['device_id'];
  }
}
