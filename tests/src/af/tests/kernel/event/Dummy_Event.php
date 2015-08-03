<?php
namespace af\tests\kernel\event;

use af\kernel\actor\events\Remote_Event;

class Dummy_Event extends Remote_Event {
  public static $DUMMY_EVENT_NAME1 = "dummy_event_name1";
  public static $DUMMY_EVENT_NAME2 = "dummy_event_name1";

  public function __construct($args) {
    parent::__construct($args, array('', '', 'construct3'));
  }

  public function construct3($name, $var1, $var2) {
    $this->set_name($name);
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
