<?php
require_once 'apdt/kernel/event/event.php';

class Actor_Error_Event extends Event {
  public static $ACTOR_ERROR_EVENT = 'actor_error_event';

  public function init_with_error_message($error_message) {
    parent::init(self::$ACTOR_ERROR_EVENT);
    $this->set_data(array("message"=>$error_message));
  }
}


