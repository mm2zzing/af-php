<?php
namespace af\plugins\uri;

use af\kernel\core\Kernel;
use af\kernel\actor\Component;

/**
 * @class Uri
 *
 * @brief 유저가 입력한 URI 정보를 조회하는 컴포넌트
 * 
 * @author Lee, Hyeon-gi
 */
class Uri extends Component {
  private $paser;

  public function parse($request_uri) {
    $this->parser = new Uri_Parser($_SERVER['SCRIPT_NAME']);
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
      $actor = Kernel::get_instance()->new_object('af\kernel\actor\Actor', '/sys/uri');
      $instance = $actor->add_component('af\plugins\uri\Uri');
    }
    return $instance;
  }
}
