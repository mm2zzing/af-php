<?php
namespace apdos\plugins\environment;

class Environment extends Component {

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $actor = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/sys/environment');
      $instance = $actor->add_component('apdos\plugins\environment/Environment');
    }
    return $instance;
  }
}
