<?php
class Loader {
  public function __construct() {
  }

  public function start() {
    $this->add_include_path();
    $this->load_system();
  }

  /**
   * require_once시에 apdt의 모듈을 간편하게 불러올 수 있도록 
   * include_path를 추가한다.
   */
  private function add_include_path() {
    $current_dir = dirname(__FILE__);
    $include_path = get_include_path();
    $include_path .= (':' . $current_dir . '/../../..');
    set_include_path($include_path);
  }

  private function load_system() {
    $this->include_module('kernel/core/entry');
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
    require_once (string) 'apdt/' . strtolower($module_path) . '.php';
  }

  public function include_modules($module_parent_path, $modules) {
    foreach ($modules as $module_name) {
      $this->include_module($module_parent_path . '/' . $module_name);
    }
  }

  private function load_module($module_path) {
    require_once (string) 'apdt/' .  $module_path . '.php';
  }

  public static function get_instance() {
    static $instance = null;
    if (null == $instance)
      $instance = new Loader();
    return $instance;
  }
}
