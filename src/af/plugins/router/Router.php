<?php
namespace af\plugins\router;

use af\kernel\core\Kernel;
use af\kernel\actor\Component;
use af\kernel\core\Object_Converter;
use af\plugins\router\dto\Router_DTO;
use af\plugins\router\dto\Null_Router_DTO;
use af\plugins\router\_common_\Register_Get_Finder;
use apods\plugins\router\Controller_Runner;

class Router extends Component {
  private $router;

  public function __construct() {
    $this->router = new Null_Router_DTO();
  }

  public function load($register_gets) {
    $this->router = new Router_DTO($register_gets);
  }

  /**
   * 현재 URI를 처리해야할 Routing 정보를 조회
   *
   * @return Register_Get_DTO
   */
  public function get_register_get($uri) {
    $finder = new Register_Get_Finder($this->router->get_register_gets());
    return $finder->find($uri);
  } 

  public function has_register_get($uri) {
    return !$this->get_register_get($uri)->is_null() ? true : false;
  }

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $actor = Kernel::get_instance()->new_object('af\kernel\actor\Actor', '/sys/router');
      $instance = $actor->add_component('af\plugins\router\Router');
    }
    return $instance;
  }
}
