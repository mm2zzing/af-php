<?php
namespace apdos\kernel\actor;

use apdos\kernel\core\Kernel;
use apdos\kernel\event\Event_Dispatcher;

class Component extends Event_Dispatcher {
  private $parent_actor;
  private $properties = array();

  public function set_parent($actor) {
    $this->parent_actor = $actor;
  }

  public function get_parent() {
    return $this->parent_actor;
  }

  public function is_null() {
    return false;
  }

  public function set_property($name, $component) {
    $this->properties[$name] = $component;
  }

  public function get_property($name) {
    if (isset($this->properties[$name]))
      return $this->properties[$name];
    return new Null_Component();
  }

  public static function create($component_class, $path) {
    $actor = Kernel::get_instance()->new_object('apdos\\kernel\\actor\\Actor', $path);
    return $actor->add_component($component_class);
  }
}
