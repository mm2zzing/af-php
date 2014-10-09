<?php
require_once 'apdt/kernel/core/node.php';

/**
 * @class Kernel
 *
 * @brieif Node 객체를 생성 관리하는 클래스
 */
class Kernel {
  public function __construct() {
  }

  /**
   * Node 객체를 생성한다.
   *
   * @param node_class_name 노드 클래스명
   * @param node_Path 노드 경로
   * @return 생성한 노드 객체
   */
  public function new_object($node_class_name, $node_path) {
    $tokens = explode('/', $node_path);
    $name = $tokens[count($tokens) - 1];

    $result = new $node_class_name($name);
    return $result;
  }

  public function delete_object($node_path) {
  }

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $instance = new Kernel();
    }
    return $instance;
  }
}
