<?php
namespace apdos\kernel\core;

/**
 * @class Loader
 * 
 * @brief Loader는 APODS의 소스 파일 모듈을 로드하는 클래스이다.
 *        AutoLoader는 PRS규약에 따라 정의해야 하는데 Loader클래스는 AutoLoader의 역할이
 *        아닌 명시적으로 모듈을 로드하는 기능이기 때문에 규약에 맞출 필요는 없다.
 *        Composer는 클래스가 아닌 것들을 로드하는 방법을 제공하지 않으므로 클래스외의 다른 모듈은
 *        Loader 클래스를 이용하여 로드하게 된다.

 * @author Lee Hyeon-gi
 */
class Loader {
  public function __construct() {
  }

  public function start() {
    $this->add_include_path();
    $this->load_system();
  }

  /**
   * require_once시에 apdos의 모듈을 간편하게 불러올 수 있도록 
   * include_path를 추가한다.
   */
  private function add_include_path() {
    $current_dir = dirname(__FILE__);
    $include_path = get_include_path();
    $include_path .= (':' . $current_dir . '/../../..');
    set_include_path($include_path);
  }

  private function load_system() {
    $this->include_module('apdos/kernel/core/entry');
  }

  /**
   * Apdt의 모듈을 로드. 메서드안에서 모듈을 로드하는 경우 require 대신 사용하는
   * 메서드로 통일성을 맞춰주는 역할을 할 뿐이다.
   * 반대로 require로 모듈을 로드하는 일련의 코드중간에서 include_module메서드를
   * 통해 모듈을 가져오는 것 역시 어색하다.
   *
   * @param module_path 모듈의 Path
   */
  public function include_module($module_path) {
    $module_path = str_replace('\\', '/', $module_path);
    require_once (string) strtolower($module_path) . '.php';
  }

  public function include_modules($module_parent_path, $modules) {
    foreach ($modules as $module_name) {
      $this->include_module($module_parent_path . '/' . $module_name);
    }
  }

  private function load_module($module_path) {
    require_once (string) $module_path . '.php';
  }

  public static function get_instance() {
    static $instance = null;
    if (null == $instance)
      $instance = new Loader();
    return $instance;
  }
}
