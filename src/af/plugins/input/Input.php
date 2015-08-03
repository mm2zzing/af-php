<?php
namespace af\plugins\input;

use af\kernel\core\Kernel;
use af\kernel\actor\Component;

class Input extends Component {
  private $input;
  private $filter_enable = true;

  public function __construct() {
  }

  public function get($key, $default_value = '') {
    if (isset($_GET[$key]))
      return $_GET[$key];
    if (isset($_POST[$key]))
      return $_POST[$key];
    return $default_value;
  }

  public function has($key) {
    if (isset($_GET[$key]) || isset($_POST[$key]))
      return true;
    return false;
  }

  public function get_ip() {
    return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
  }

  public function get_user_agent() {
    return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
  }

  public function xss_filter($contents) {
  }

  public function toggle_filter($enable) {
    $this->filter_enable = $enable;
  }

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $actor = Kernel::get_instance()->new_object('af\kernel\actor\Actor', '/sys/input');
      $instance = $actor->add_component('af\plugins\input\Input');
    }
    return $instance;
  }
}
