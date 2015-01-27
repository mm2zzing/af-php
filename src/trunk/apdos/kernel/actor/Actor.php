<?php
namespace apdos\kernel\actor;

use apdos\kernel\core\Root_Node;
use apdos\kernel\actor\Component;
use apdos\kernel\actor\Null_Component;

class Actor extends Root_Node {
  private $components = array();

  /**
   * 컴포넌트 추가
   * 
   * @param component_class_name String 컴포넌트 클래스 명
   */
  public function add_component($component_class_name) {
    $result = new $component_class_name();
    $result->set_parent($this);
    array_push($this->components, $result);
    return $result;
  }

  public function get_component($component_class_name) {
    foreach ($this->components as $component) {
      $class_name = get_class($component);
      if (0 == strcmp($class_name, $component_class_name))
        return $component;
    }
    return new Null_Component();
  }

  public function remove_component($component_class_name) {
    for ($i = 0; $i < count($this->components); $i++) {
      $component = $this->components[$i];
      $class_name = get_class($component);
      if (0 == strcmp($class_name, $component_class_name)) {
        array_splice($this->components, $i, 1);
      }
    }
  }
}
