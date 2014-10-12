<?php
require_once 'apdt/kernel/core/node.php';
require_once 'apdt/kernel/core/error/node_is_exist_error.php';

/**
 * @class Kernel
 *
 * @brieif Node 객체를 생성 관리하는 클래스
 */
class Kernel {
  private $objects = array();

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
    if (!$this->find_object($node_path)->is_null())
      throw new Node_Is_Exist_Error('Node is already exist. path is ' . $node_path);
    $tokens = explode('/', $node_path);
    $name = $tokens[count($tokens) - 1];

    $result = new $node_class_name($name, $node_path);
    array_push($this->objects, $result);
    return $result;
  }

  public function delete_object($node_path) {
    for ($i = 0; $i < count($this->objects); $i++) {
      $node = $this->objects[$i];
      if (0 == strcmp($node->get_path(), $node_path)) {
        array_splice($this->objects, $i, 1);
        break;
      }
    }
  }

  public function find_object($node_path) {
    foreach ($this->objects as $object) {
      if (0 == strcmp($object->get_path(), $node_path))
        return $object;
    }
    return new Null_Node();
  }

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $instance = new Kernel();
    }
    return $instance;
  }
}
