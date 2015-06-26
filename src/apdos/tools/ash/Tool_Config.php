<?php
namespace apdos\tools\ash;

use apdos\kernel\core\Kernel;
use apdos\kernel\actor\Component;
use apdos\plugins\config\Config;

class Tool_Config extends Component {
  public function select_tool($tool_actor, $tool_path) {
    // environment 옵션은 application의 설정을 따른다.
    $app_environment = Config::get_instance()->get_enviroment();
    $this->config = $tool_actor->add_component('apdos\plugins\config\Config');
    $this->config->select_application($tool_path, $app_environment);
  }

  public function set($path, $value, $persistent = false) {
    $this->config->set($path, $value, $persistent);
  }

  public function push($path, $value, $persistent = false) {
    $this->config->push($path, $value, $persistent);
  }

  public function clear($config_name, $persistent = false) {
    $this->config->clear($config_name, $persistent);
  }

  public function get($path) {
    return $this->config->get($path);
  }

  /**
   * Tool의 설정 모듈을 리턴한다.
   *
   * @return Config
   */
  protected function get_config() {
    if (null == $this->config)
      throw new Ash_Error('config is null', Ash_Error.INVALID_CONFIG);
    return $this->config; 
  }

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $actor = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/sys/tool_config');
      $instance = $actor->add_component('apdos\tools\ash\Tool_Config');
    }
    return $instance;
  }

  private $config = null;
}
