<?php
namespace apdos\plugins\config; 

use apdos\kernel\core\Kernel;
use apdos\kernel\actor\Component;
use apdos\kernel\core\Object_Converter;
use apdos\plugins\resource\File_Error;
use apdos\plugins\config\errors\Config_Error;

/**
 * @class Config
 *
 * @brief 특정 어플리케이션의 설정을 로드하는 객체. 
 *        Config에서 관리하는 설정 파일은 읽기만 가능하다.
 *
 *        @TODO Config와 Etc 설정파일에서 json 처리 코드 리펙토링 필요
 * @authro Lee, Hyeon-gi
 */
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
   * 설정 정보를 변경 한다. 변경하기 위해서는 정보가 미리 로드되어 있어야 한다.
   * 
   * 시스템이 구동되면서 동적으로 설정을 변경하고자 할떄는 persistent 옵션을 false로 설정
   * 변경된 설정 정보가 파일에 반영되길 원한다면 persistenent 옵션을 true로 설정
   *
   * @param path string 설정 패스 
   * @param value object 설정 데이터
   */
  public function set($path, $value, $persistent = false) {
    $tokens = explode('.', $path);
    $config_name = $tokens[0];
    if (!isset($this->configs[$config_name]))
      throw new Config_Error('Not loaded config:' . $path);
    $current = &$this->configs[$config_name];
    for ($i = 1; $i < count($tokens); $i++) {
      $name = $tokens[$i]; 
      if ($i == (count($tokens) - 1))
        $current->$name = $value;
      else { 
        if (!isset($current->$name))
          $current->$name = new \stdClass();
        $current = &$current->$name;
      }
    }

    if ($persistent)
      $this->save($config_name);
  }

  public function push($path, $value, $persistent = false) {
    $tokens = explode('.', $path);
    $config_name = $tokens[0];
    if (!isset($this->configs[$config_name]))
      throw new Config_Error('Not loaded config:' . $path);
    $current = &$this->configs[$config_name];
    for ($i = 1; $i < count($tokens); $i++) {
      $name = $tokens[$i]; 
      if ($i == (count($tokens) - 1)) {
        if (!is_array($current->$name))
          throw new Config_Error("Target path is not array", Config_Error::PUSH_FAILED);
        array_push($current->$name, $value);
      }
      else { 
        if (!isset($current->$name))
          $current->$name = new \stdClass();
        $current = &$current->$name;
      }
    }
    if ($persistent)
      $this->save($config_name);
  }

  public function clear($config_name, $persistent = false) {
    unset($this->configs[$config_name]);
    if ($persistent)
      $this->delete($config_name);
  }

  private function delete($config_name) {
    $env = $this->get_enviroment();
    $file_path = "$this->application_path/config/$env/$config_name.json";
    $file = Component::create('apdos\plugins\resource\File', '/app/files/' . $config_name);
    $file->delete($file_path);
    $file->get_parent()->release();
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
    if (!isset($this->configs[$config_name]))
      $this->load($config_name);

    $current = $this->configs[$config_name];
    for ($i = 1; $i < count($tokens); $i++) {
      $name = $tokens[$i];
      if (!isset($current->$name))
        throw new Config_Error('Not exist path:' + $path, Config_Error::GET_FAILED);
      $current = $current->$name;
    }
    return $current;
  }

  private function load($config_name) {
    $env = $this->get_enviroment();
    $file_path = "$this->application_path/config/$env/$config_name.json";
    $file = Component::create('apdos\plugins\resource\File', '/app/files/' . $config_name);
    try {
      $file->load($file_path);
      $parse_data = json_decode($file->get_contents());
      switch (json_last_error()) {
        case JSON_ERROR_NONE:
          break;
        case JSON_ERROR_DEPTH:
          throw new \Exception('Maximum stack depth exceeded');
          break;
        case JSON_ERROR_STATE_MISMATCH:
          throw new \Exception('Underflow or the modes mismatch');
          break;
        case JSON_ERROR_CTRL_CHAR:
          throw new \Exception('Unexpected control character found');
          break;
        case JSON_ERROR_SYNTAX:
          throw new \Exception('Syntax error, malformed JSON');
          break;
        case JSON_ERROR_UTF8:
          throw new \Exception('Malformed UTF-8 characters, possibly incorrectly encoded');
          break;
        default:
          throw new \Exception('Unknown error');
          break;
      }
      $this->configs[$config_name] = Object_Converter::to_object($parse_data);
      $file->get_parent()->release();
    }
    catch (File_Error $e) {
      $file->get_parent()->release();
      throw new Config_Error($e->getMessage(), Config_Error::LOAD_FAILED);
    }
    catch (\Exception $e) {
      $file->get_parent()->release();
      throw new Config_Error($e->getMessage(), Config_Error::LOAD_FAILED);
    }
  }

  private function save($config_name) {
    $env = $this->get_enviroment();
    $file_path = "$this->application_path/config/$env/$config_name.json";
    $file = Component::create('apdos\plugins\resource\File', '/app/files/' . $config_name);
    try {
      $encode_data = json_encode($this->configs[$config_name], JSON_PRETTY_PRINT);
      $file->save($file_path, $encode_data);
      $file->get_parent()->release();
    }
    catch (File_Error $e) {
      $file->get_parent()->release();
      throw new Config_Error($e->getMessage(), Config_Error::SAVE_FAILED);
    }
    catch (\Exception $e) {
      $file->get_parent()->release();
      throw new Config_Error($e->getMessage(), Config_Error::SAVE_FAILED);
    }
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
