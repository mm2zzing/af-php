<?php
namespace af\kernel\actor\property\events;

use af\kernel\event\Event;

class Property_Event extends Event {
  public static $CHANGE = 'change';

  public function __construct($event_name, $property_name) {
    $data = array("property_name"=>$property_name);
    parent::__construct(array($event_name, $data));
  }

  public function get_property_name() {
    $data = $this->get_data();
    return $data->property_name;
  }
}
 
