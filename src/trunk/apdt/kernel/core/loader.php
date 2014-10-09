<?php
class Loader {
  public function __construct() {
    $this->add_include_path();
    $this->load_system();
  }

  private function add_include_path() {
    $current_dir = dirname(__FILE__);
    $include_path = get_include_path();
    $include_path .= (':' . $current_dir . '/../../..');
    echo $include_path;
    set_include_path($include_path);
  }

  private function load_system() {
    $this->include_module('kernel/core/entry');
  }

  /**
   * Apdt의 모듈을 로드 한다.
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
