<?php
namespace apdos\plugins\config; 

use apdos\kernel\core\Kernel;
use apdos\kernel\actor\Component;
use apdos\kernel\core\Object_Converter;
use apdos\plugins\resource\File_Error;
use apdos\plugins\config\errors\Config_Error;

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

  public function get_application_path() {
    return $this->application_path;
  }

  public function get_enviroment() {
    return $this->environment;
  }

  /**
   * 설정 정보를 저장한다.
   *
   * @param path string 설정 패스 
   */
  public function set($path, $value) {
    $tokens = explode('.', $path);
    $config_name = $tokens[0];
    $item_name = $tokens[1];
    $this->configs[$config_name]->{$this->environment}->$item_name = $value;
  }

  /**
   * 설정 정보를 리턴한다. set 함수에 의해 이미 값이 설정되어 있다면 파일에서 로드 하지 않는다.
   *
   * @param path string 설정 패스 
   * @return object 설정 값
   */
  public function get($path) {
    $tokens = explode('.', $path);
    $config_name = $tokens[0];
    $item_name = $tokens[1];
    if (!isset($this->configs[$config_name]) ||
        !isset($this->configs[$config_name]->{$this->environment}->$item_name)) {
      $this->load($config_name);
    }
    return $this->configs[$config_name]->{$this->environment}->$item_name;
  }

  private function load($config_name) {
    $file_path = "$this->application_path/config/$config_name.json";
    $file = Component::create('apdos\plugins\resource\File', '/app/files/' . $config_name);
    try {
      $file->load($file_path);
      $parse_data = json_decode($file->get_contents());
      $this->configs[$config_name] = Object_Converter::to_object($parse_data);
    }
    catch (File_Error $e) {
      throw new Config_Error($e->getMessage(), Config_Error::LOAD_FAILED);
    }
  }

  public function clear_cache($path) {
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
