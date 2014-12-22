<?php
namespace apdos\kernel\actor;

use apdos\kernel\core\Kernel;

class Component {
  private $parent_actor;

  public function set_parent($actor) {
    $this->parent_actor = $actor;
  }

  public function get_parent() {
    return $this->parent_actor;
  }

  public function is_null() {
    return false;
  }

  public static function create($component_class, $path) {
    $actor = Kernel::get_instance()->new_object('apdos\\kernel\\actor\\Actor', $path);
    return $actor->add_component($component_class);
  }
}
