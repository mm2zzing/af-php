<?php
namespace af\kernel\actor\events;

use af\kernel\event\Event;

class Actor_Error_Event extends Event {
  public static $ACTOR_ERROR_EVENT = 'actor_error_event';

  public function __construct($error_message) {
    $data = array("message"=>$error_message);
    parent::__construct(array(self::$ACTOR_ERROR_EVENT, $data));
  }
}


