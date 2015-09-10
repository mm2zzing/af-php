<?php
namespace af\kernel\actor;

use af\kernel\core\Root_Node;
use af\kernel\actor\Component;
use af\kernel\actor\events\Component_Event;
use af\kernel\actor\events\Actor_Event;
use af\kernel\actor\Null_Component;
use af\kernel\core\Kernel;

class Actor extends Root_Node {
  const RANDOM_NAME_SIZE = 5;

  private $components = array();

  /**
   * 컴포넌트 이름을 이용하여 추가
   *
   * @param component_class_name String 컴포넌트 클래스 명(네임스페이스 포함)
   */
  public function add_component($component_class_name) {
    $component = new $component_class_name();
    $component->set_parent($this);
    array_push($this->components, $component);

    $start_event = new Component_Event(array(Component_Event::$START));
    $component->async_dispatch_event($start_event);
    return $component;
  }

  public function get_component($component_class_name) {
    foreach ($this->components as $component) {
      $class_name = get_class($component);
      if (0 == strcmp($class_name, $component_class_name))
        return $component;
    }
    return new Null_Component();
  }

  public function get_components($component_class_name) {
    $result = array();
    foreach ($this->components as $component) {
      $class_name = get_class($component);
      if (0 == strcmp($class_name, $component_class_name))
        array_push($result, $component);
    }
    return $result;
  }

  public function remove_component($component_class_name) {
    for ($i = 0; $i < count($this->components); $i++) {
      $component = $this->components[$i];
      $class_name = get_class($component);
      if (0 == strcmp($class_name, $component_class_name)) { 
        $component->release();
        array_splice($this->components, $i, 1);
      }
    }
  }

  // override
  public function dispatch_event($event) {
    parent::dispatch_event($event);
    for ($i = 0; $i < count($this->components); $i++) {
      $this->components[$i]->dispatch_event($event);
    }
  }

  public function release() {
    parent::release();
    $event = new Actor_Event(array(Actor_Event::$DESTROY));
    $this->dispatch_event($event);

    for ($i = 0; $i < count($this->components); $i++) {
      $this->components[$i]->release();
    }
    $this->components = array();
  }

  public function update() {
    $this->update_events();
  }

  public function update_events() {
    foreach ($this->components as $component)
      $component->update_event();
    parent::update_event(); 
  }

  public static function create($path = '') {
    if ('' == $path) {
      $actor_id = substr(md5(uniqid(rand())), 0, self::RANDOM_NAME_SIZE);
      $path = '/tmp/' . $actor_id;
    }
    return Kernel::get_instance()->new_object('af\\kernel\\actor\\Actor', $path);
  }
}
