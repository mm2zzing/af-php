<?php
namespace apdos\kernel\env;

use apdos\kernel\core\Kernel;
use apdos\kernel\actor\Actor;
use apdos\kernel\actor\Component;

class Environment extends Component {

  public function is_64bit_machine() {
    return PHP_INT_MAX == 9223372036854775807 ? true : false;
  }

  /**
   * 프로시저 아이디를 리턴
   *
   * return int 프로시저아이디
   */
  public function get_process_id() {
    return getmypid();
  }

  /**
   * 
   */
  public function get_host_name() {
    return gethostname();
  }

  public function get_os_information() {
    return php_uname();
  }

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $actor = Kernel::get_instance()->new_object(Actor::get_class_name(), '/sys/environment');
      $instance = $actor->add_component(Environment::get_class_name());
    }
    return $instance;
  }
}
