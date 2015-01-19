<?php
namespace apdos\plugins\input;

use apdos\kernel\core\Kernel;
use apdos\kernel\actor\Component;

class Input extends Component {
  private $input;
  private $filter_enable = true;

  public function __construct() {
  }

  public function get($key) {
    if (isset($_GET[$key]))
      return $_GET[$key];
    if (isset($_POST[$key]))
      return $_POST[$key];
    return '';
  }

  public function has($key) {
    if (isset($_GET[$key]))
      return true;
    if (isset($_POST[$key]))
      return true;
    return false;
  }

  public function get_ip() {
    if (isset($_SERVER['REMOTE_ADDR']))
      return $_SERVER['REMOTE_ADDR'];
    return 'Unknown';
  }

  public function get_user_agent() {
    if (isset($_SERVER['HTTP_USER_AGENT']))
      return $_SERVER['HTTP_USER_AGENT'];
    return 'Unknown';
  }

  public function xss_filter($contents) {
  }

  public function toggle_filter($enable) {
    $this->filter_enable = $enable;
  }

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $actor = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/sys/input');
      $instance = $actor->add_component('apdos\plugins\input\Input');
    }
    return $instance;
  }
}
