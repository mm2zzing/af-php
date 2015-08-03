<?php
namespace af\plugins\database\connecters\mysql;

use af\kernel\actor\Component;
use af\kernel\actor\events\Component_Event;
use af\plugins\database\base\rdb\RDB_Session;
use af\plugins\database\connecters\mysql\MySQL_Connecter;
use af\plugins\database\connecters\mysql\MySQL_Schema;
use af\plugins\database\connecters\mysql\MySQL_Util;

class MySQL_Session extends RDB_Session { 
  public function __construct() {
    $that = $this;
    $this->add_event_listener(Component_Event::$START, function($event) use(&$that) {
      $that->set_session_properties(); 
    });
    parent::__construct();
  }

  private function set_session_properties() {
    $this->set_property('connecter_class_name', MySQL_Connecter::get_class_name());
    $this->set_property('schema_class_name', MySQL_Schema::get_class_name());
    $this->set_property('util_class_name', MySQL_Util::get_class_name());
  }
}
 
