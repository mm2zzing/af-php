<?php
namespace apdos\plugins\uri;

use apdos\kernel\core\Kernel;
use apdos\kernel\actor\Component;

class Uri extends Component {
  public function get_uri_string() {
    $uri = $_SERVER['REQUEST_URI'];
    $tokens = split($_SERVER['SCRIPT_NAME'], $uri);
    // URI가 공백이거나 슬래시 한개면 맨 상위 루트를 의미한다.
    if ($tokens[0] == '' || $tokens[0] == '/')
      return '/';
    // URI 마지막 뒤에 붙은 슬래시는 무시한다. 루트가 아닌 경우 마지막 슬래시는 의미가 없다.
    return rtrim($tokens[0], '/');
  }

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $actor = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/sys/uri');
      $instance = $actor->add_component('apdos\plugins\uri\Uri');
    }
    return $instance;
  }
}
