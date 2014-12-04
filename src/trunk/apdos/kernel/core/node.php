<?php
require_once 'apdos/kernel/event/event_dispatcher.php';

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

class Null_Node extends Node {
  public function __construct() {
    parent::__construct('null', '/null');
  }

  public function is_null() {
    return true;
  }
}
