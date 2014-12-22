<?php
namespace apdos\kernel\log;

use apdos\kernel\core\Kernel;
use apdos\kernel\actor\Component;

class Logger extends Component {
  public function debug($tag, $message) {
    echo '[' . $tag . '] ' . $message;
  }

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $actor = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/sys/logger');
      $instance = $actor->add_component('apdos\kernel\log\Logger');
    }
    return $instance;
  }
}
