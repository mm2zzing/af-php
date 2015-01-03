<?php
namespace apdos\kernel\actor\events;

use apdos\kernel\event\Event;

class Actor_Error_Event extends Event {
  public static $ACTOR_ERROR_EVENT = 'actor_error_event';

  public function init_with_error_message($error_message) {
    parent::init_with_name(self::$ACTOR_ERROR_EVENT);
    $this->set_data(array("message"=>$error_message));
  }
}


