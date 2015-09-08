<?php
namespace af\plugins\config; 

use af\kernel\core\Kernel;
use af\kernel\actor\Component;
use aol\components\config\Setting;

/**
 * @class Config
 *
 * @authro Lee, Hyeon-gi
 */
class Config extends Component {
  private $setting;

  public function __construct() {
    $this->setting = new Setting();
  }

  public function select_application($application_path, $environment) {
    $this->setting->select_application($application_path, $environment);
  } 

  public function get_application_path() {
    return $this->setting->get_application_path();
  }

  public function get_enviroment() {
    return $this->setting->get_enviroment();
  }

  /**
   * 설정 정보를 변경 한다. 변경하기 위해서는 정보가 미리 로드되어 있어야 한다.
   * 
   * 시스템이 구동되면서 동적으로 설정을 변경하고자 할떄는 persistent 옵션을 false로 설정
   * 변경된 설정 정보가 파일에 반영되길 원한다면 persistenent 옵션을 true로 설정
   *
   * @param path string 설정 패스 
   * @param value object 설정 데이터
   * @param persistent bool 파일에 반영 여부
   */
  public function set($path, $value, $persistent = false) {
    $this->setting->set($path, $value, $persistent);
  }

  public function push($path, $value, $persistent = false) {
    $this->setting->push($path, $value, $persistent);
  }

  public function clear($config_name, $persistent = false) {
    $this->setting->clear($config_name, $persistent);
  }

  /**
   * 설정 정보를 리턴한다. 
   *
   * @param path string 설정 패스 
   * @return object 설정 값
   */
  public function get($path) {
    return $this->setting->get($path);
  }

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $actor = Kernel::get_instance()->new_object('af\kernel\actor\Actor', '/sys/config');
      $instance = $actor->add_component('af\plugins\config\Config');
    }
    return $instance;
  }
}
