<?php
namespace apdos\plugins\sharding;

use apdos\kernel\core\Kernel;
use apdos\kernel\actor\Component;

class Router extends Component {
  private $router;

  public function __construct() {
    //$this->router = new Null_Shard_Config_DTO();
  }

  public function load($shard_config) {
    //$this->router = new Shard_Config_DTO($shard_config);
  }

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $actor = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/sys/srouter');
      $instance = $actor->add_component('apdos\plugins\sharding\Router');
    }
    return $instance;
  }
}
