<?php
namespace apdos\plugins\uri;

use apdos\kernel\core\Kernel;
use apdos\kernel\actor\Component;

class Uri extends Component {
  private $uri_string;
  private $uri_tokens;

  public function parse($request_uri) {
    $request_uri = $this->extract_uri($request_uri);
    $this->uri_string = '/'. $request_uri;
    if ($this->uri_string == '/')
      $this->uri_tokens = array();
    else
      $this->uri_tokens = split('/', $request_uri);
  }

  public function get_segment($index, $default = '') {
    if (isset($this->uri_tokens[$index]))
      return $this->uri_tokens[$index];
    return $default;
  }

  public function get_uri_string() {
    return $this->uri_string;
  }

  private function extract_uri($uri) {
    $tokens = split($_SERVER['SCRIPT_NAME'], $uri);
    if ($tokens[0] == '')
      return trim($tokens[1], '/');
    else
      return trim($tokens[0], '/');
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
