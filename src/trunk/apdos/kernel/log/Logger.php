<?php
namespace apdos\kernel\log;

use apdos\kernel\core\Kernel;
use apdos\kernel\actor\Component;
use apdos\kernel\core\Time;

class Logger extends Component {
  public static function debug($tag, $message) {
    $ctime = Time::get_instance()->get_timestamp();
    echo '[' . $ctime . '][' . $tag . '][DEBUG] ' . $message . PHP_EOL;
  }

  public static function warning($tag, $message) {
    $ctime = Time::get_instance()->get_timestamp();
    echo '[' . $ctime . '][' . $tag . '][WARN] ' . $message . PHP_EOL;
  }

  public static function trace($tag, $message) {
    $ctime = Time::get_instance()->get_timestamp();
    echo '[' . $ctime . '][' . $tag . '][TRACE] ' . $message . PHP_EOL;
  }

  public static function info($tag, $message) {
    $ctime = Time::get_instance()->get_timestamp();
    echo '[' . $ctime . '][' . $tag . '][INFO] ' . $message . PHP_EOL;
  }

  public static function error($tag, $message) {
    $ctime = Time::get_instance()->get_timestamp();
    echo '[' . $ctime . '][' . $tag . '][ERROR] ' . $message . PHP_EOL;
  }

  public static function fatal($tag, $message) {
    $ctime = Time::get_instance()->get_timestamp();
    echo '[' . $ctime . '][' . $tag . '][FATAL] ' . $message . PHP_EOL;
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
