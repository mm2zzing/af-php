<?php
namespace af\kernel\core;

use af\kernel\core\Node;
use af\kernel\core\Object;
use af\kernel\core\Null_Node;
use af\kernel\core\Root_Node;
use af\kernel\core\errors\Node_Is_Exist_Error;
use af\kernel\core\Kernel;
use af\kernel\user\User_Server;
use af\kernel\core\permission\Owner;
use af\kernel\core\permission\Permission;
use af\kernel\core\permission\enums\Access_Type;

/**
 * @class Kernel
 *
 * @brieif Node 객체를 생성 관리하는 클래스
 */
class Kernel extends Object {
  public function __construct() {
    $this->root = new Root_Node(array(
        '/', 
        new Owner(User_Server::ROOT_USER),
        $this->create_permission()
    ));
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

    $first_key = key($tokens);
    end($tokens);
    $last_key = key($tokens);
    foreach ($tokens as $key=>$token) {
      if ($key == $first_key)
        continue;
      $node = $current_node->find_child($token);
      if ($key == $last_key) {
        $new_node = new $node_class_name(array($token, $this->create_owner(), $this->create_permission()));
        $current_node->add_child($new_node);
        $current_node = $new_node;
      }
      else {
        if ($node->is_null()) {
          $new_node = new Root_Node(array($token, $this->create_owner(), $this->create_permission()));
          $current_node->add_child($new_node);
          $current_node = $new_node;
        }
        else
          $current_node = $node;
      }
    }
    return $current_node;
  }

  private function create_owner() {
    $current_user = User_Server::get_instance()->get_login_user();
    return new Owner($current_user->get_name());
  }

  private function create_permission() {
    return new Permission(array(
        Access_Type::FLAG_ALL, 
        Access_Type::FLAG_ALL, 
        Access_Type::FLAG_READ));
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
      $first_key = key($tokens);
      $current_node = $this->root;
      if (count($tokens) == 1) {
        $current_node = $current_node->find_child($tokens[0]);
      }
      else {
        foreach ($tokens as $key=>$token) {
          if ($key == $first_key)
            continue;
          $find_node = $current_node->find_child($token);
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

  private $root;
}
