<?php
require_once 'apdt/kernel/event/event_dispatcher.php';

class Node extends Event_Dispatcher {
  private $name;

  public function __construct($name) {
    $this->name = $name;
  }

  public function get_name() {
    return $this->name;
  }

  public function get_path() {
    return '/test/' . $this->name;
  }
}
