<?php
namespace apdos\kernel\actor;

use apdos\kernel\core\Kernel;
use apdos\kernel\event\Event_Dispatcher;
use apdos\kernel\actor\events\Component_Event;
use apdos\kernel\actor\events\Actor_Event;
use apdos\kernel\actor\property\Root_Property;
use apdos\kernel\actor\property\Null_Property;
use apdos\kernel\actor\property\events\Property_Event;

/**
 * @class Component
 *
 * @brieif Component는 로직 처리를 위한 기본 베이스로 사용한다.
           set_property를 통해 다른 Component, Actor 값을 넘겨서 사용할 수 있다.
           Componentdhk  Actor를 일반 멤버 변수로 저장하는 경우 반드시 Destroy 이벤트를 체크해서
           파괴 여부를 확인해야 하고 Actor framework는 Component의 property기반으로 속성값을 관리하기
           때문에 set_property를 통해 값을 넘겨주는 것을 추천한다.
 *
 * @author Lee, Hyeon-gi
 */
class Component extends Event_Dispatcher {
  private $parent_actor;
  private $properties = array();

  public function __construct() {
  }

  public function set_parent($actor) {
    $this->parent_actor = $actor;
  }

  public function get_parent() {
    return $this->parent_actor;
  }

  public function is_null() {
    return false;
  }

  public function get_component($component_class_name) {
    return $this->parent_actor->get_component($component_class_name);
  }

  public function get_components($component_class_name) {
    return $this->parent_actor->get_components($component_class_name);
  }

  /**
   * 속성값을 설정한다. Component를 속성으로 처리하는 경우
   * 설정한 Component 객체가 삭제되면 자동으로 Null_Property로 변경된다. (Actor, Component)
   */
  public function set_property($name, $value) {
    $this->regist_destory_events($name, $value);
    
    $this->properties[$name] = new Root_Property($name, $value);

    $event = new Property_Event(Property_Event::$CHANGE, $name);
    $this->dispatch_event($event);
  }

  public function unset_property($name) {
    $this->properties[$name] = new Null_Property($name);
    $event = new Property_Event(Property_Event::$CHANGE, $name);
    $this->dispatch_event($event);
  }

  private function regist_destory_events($name, $value) {
    if ($value instanceof Actor) {
      $other = $this;
      $value->add_event_listener(Actor_Event::$DESTROY, function($event) use(&$other, &$name) {
        $other->unset_property($name);
      });
    }
    if ($value instanceof Component) {
      $other = $this;
      $value->add_event_listener(Component_Event::$DESTROY, function($event) use(&$other, &$name) {
        $other->unset_property($name);
      });
    }
  }
  
  public function get_property($name) {
    if (isset($this->properties[$name]))
      return $this->properties[$name];
    return new Null_Property($name);
  }

  public function release() {
    $event = new Component_Event(array(Component_Event::$DESTROY));
    $this->dispatch_event($event);
  }

  public static function create($component_class, $path) {
    $actor = Kernel::get_instance()->new_object('apdos\\kernel\\actor\\Actor', $path);
    return $actor->add_component($component_class);
  }
}
