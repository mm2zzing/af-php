<?php
namespace apdos\kernel\core;

use apdos\kernel\core\Node;
use apdos\kernel\core\Null_Node;
use apdos\kernel\core\Root_Node;
use apdos\kernel\core\errors\Node_Is_Exist_Error;
use apdos\kernel\core\Kernel;

/**
 * @class Kernel
 *
 * @brieif Node 객체를 생성 관리하는 클래스
 */
class Kernel {
  private $root;

  public function __construct() {
    $this->root = new Root_Node(array('/', ''));
  }

  public function has_object($node_path) {
    return true;
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
    
    $current_node = $this->root;
    $tokens = explode('/', $node_path);

    $start_index = 1;
    $last_index = count($tokens) - 1;
    for ($i = $start_index; $i < count($tokens); $i++) {
      $node = $current_node->find_child($tokens[$i]);
      if ($i == $last_index) {
        $new_node = new $node_class_name(array($tokens[$i], ''));
        $current_node->add_child($new_node);
        $current_node = $new_node;
      }
      else {
        if ($node->is_null()) {
          $new_node = new Root_Node(array($tokens[$i], ''));
          $current_node->add_child($new_node);
          $current_node = $new_node;
        }
        else
          $current_node = $node;
      }
    }
    return $current_node;
  }

  /**
   * @TODO Rename to release_object
   */
  public function delete_object($node_path) {
    $this->find_object($node_path)->release();
  }

  /**
   * @TODO Rename to lookup
   */
  public function find_object($node_path) {
    if ('/' == $node_path)
      return $this->root;
    else {
      $tokens = explode('/', $node_path);
      $start_index = 1;
      $current_node = $this->root;
      if (count($tokens) == 1) {
        $current_node = $current_node->find_child($tokens[0]);
      }
      else {
        for ($i = $start_index; $i < count($tokens); $i++) {
          $find_node = $current_node->find_child($tokens[$i]);
          if ($find_node->is_null())
            return $find_node;
          $current_node = $find_node;
        }
      }
      return $current_node;
    }
  }

  public function get_root() {
    return $this->root;
  }

  public function update() {
    $this->root->update();
  }
   
  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $instance = new Kernel();
    }
    return $instance;
  }
}
