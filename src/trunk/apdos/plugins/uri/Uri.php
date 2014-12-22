<?php
namespace apdos\plugins\uri;

use apdos\kernel\core\Kernel;
use apdos\kernel\actor\Component;

class Uri extends Component {
  private $paser;

  public function parse($request_uri) {
    $this->parser = new Uri_Parser();
    $this->parser->parse($request_uri);
  }

  public function get_segment($index, $default = '') {
    $this->parser->get_segment($index, $default);
  }

  public function get_uri_string() {
    return $this->parser->get_uri_string();
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
