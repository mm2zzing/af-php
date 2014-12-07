<?php
namespace apdos\kernel\core;

use apdos\kernel\event\event_dispatcher;

class Node extends Event_Dispatcher {
  private $name;
  private $path;

  public function __construct($name, $path) {
    $this->name = $name;
    $this->path = $path;
  }

  public function get_name() {
    return $this->name;
  }

  public function get_path() {
    return $this->path;
  }

  public function is_null() {
    return false;
  }
}
