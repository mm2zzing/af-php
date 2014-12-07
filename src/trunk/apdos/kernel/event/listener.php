<?php
namespace apdos\kernel\event;

class Listener {
  private $object;
  private $object_method;

  public function __construct($object, $object_method) {
    $this->object = $object;
    $this->object_method = $object_method;
  }

  public function run($event) {
    call_user_func(array($this->object, $this->object_method), $event);
  }
}
