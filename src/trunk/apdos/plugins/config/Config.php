<?php
namespace apdos\plugins\config; 

use apdos\kernel\core\Kernel;
use apdos\kernel\actor\Component;
use apdos\kernel\core\Object_Converter;

class Config extends Component {
  private $configs;
  private $appication_path;
  private $environment;

  public function __construct() {
    $this->configs = array();
  }

  public function select_application($application_path, $environment) {
    $this->application_path = $application_path;
    $this->environment = $environment;
  }

  public function load($config_name, $cache_time = 0) {
    $file_path = "$this->application_path/config/$config_name.json";
    $file = Component::create('apdos\plugins\resource\File', '/app/files/' . $config_name);
    $file->load($file_path);
    $parse_data = json_decode($file->get_contents());
    $this->configs[$config_name] = Object_Converter::to_object($parse_data);
  }

  public function unload($config_name) {
  }

  public function get($path) {
    $tokens = explode('.', $path);
    $config_name = $tokens[0];
    $item_name = $tokens[1];
    if (!isset($this->configs[$config_name])) {
      $this->load($config_name);
    }
    return $this->configs[$config_name]->{$this->environment}->$item_name;
  }

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $actor = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/sys/config');
      $instance = $actor->add_component('apdos\plugins\config\Config');
    }
    return $instance;
  }
}
