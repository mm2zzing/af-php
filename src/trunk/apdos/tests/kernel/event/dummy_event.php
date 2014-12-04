<?php
require_once 'apdos/kernel/actor/events/remote_event.php';

class Dummy_Event extends Remote_Event {
  public static $DUMMY_EVENT_NAME1 = "dummy_event_name1";
  public static $DUMMY_EVENT_NAME2 = "dummy_event_name1";

  public function init($name, $var1, $var2) {
    parent::init($name);
    $data = array();
    $data['var1'] = $var1;
    $data['var2'] = $var2;
    $this->set_data($data);
  }

  public function get_var1() {
    return $this->data['var1'];
  }

  public function get_var2() {
    return $this->data['var2'];
  }
}
