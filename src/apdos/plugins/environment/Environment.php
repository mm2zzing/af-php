<?php
namespace apdos\plugins\environment;

use apdos\kernel\actor\Actor;
use apdos\plugins\environment\Environment;

class Environment extends Component {

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $actor = Kernel::get_instance()->new_object(Actor::get_class_name(), '/sys/environment');
      $instance = $actor->add_component(Environment::get_class_name());
    }
    return $instance;
  }
}
