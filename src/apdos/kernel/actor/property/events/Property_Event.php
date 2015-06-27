<?php
namespace apdos\kernel\actor\property\events;

use apdos\kernel\event\Event;

class Property_Event extends Event {
  public static $CHANGE = 'change';

  public function init_with_property_name($event_name, $property_name) {
    parent::init_with_name($event_name);
    $this->set_data(array("property_name"=>$property_name));
  }

  public function get_property_name() {
    $data = $this->get_data();
    return $data->property_name;
  }
}
 
