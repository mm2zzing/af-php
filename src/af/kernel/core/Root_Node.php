<?php
namespace af\kernel\core;

use af\kernel\event\Event_Dispatcher;
use af\kernel\core\Node;
use af\kernel\core\Null_Node;
use af\kernel\core\Kernel;

class Root_Node extends Node {
  public function __construct($args) {
    parent::__construct($args, array('', '', 'construct3'));
  }

  public function construct3($name, $owner, $permission) {
    $this->name = $name;
    $this->parent = new Null_Node();
    $this->childs = array();
    $this->owner = $owner;
    $this->permission = $permission;
  }

  public function get_name() {
    return $this->name;
  }

  public function get_path() {
    $nodes = array(); $current_node = $this;
    do {
      $parent_node = $current_node->get_parent();
      array_push($nodes, $current_node);
      $current_node = $parent_node;
    }
    while (!$parent_node->is_null());

    $nodes = array_reverse($nodes);
    end($nodes);
    $last_key = key($nodes);
    $result = '';
    foreach ($nodes as $key=>$node) {
      $result .= $node->get_name();
      if ($node->get_name() != '/' && $key != $last_key)
        $result .= '/';
    }
    return $result;
  }

  public function add_child($node) {
    $child = $this->find_child($node->get_name());
    if (!$child->is_null())
      throw new Node_Is_Exist_Error('Node is already exist. name is ' . $node->get_name());
    array_push($this->childs, $node);
    $node->set_parent($this);
  }

  public function find_child($name) {
    for ($i = 0; $i < count($this->childs); $i++) {
      if ($this->childs[$i]->get_name() == $name)
        return $this->childs[$i];
    }
    return new Null_Node();
  }

  public function remove_child($name) {
    for ($i = 0; count($this->childs); $i++) {
      if ($this->childs[$i]->get_name() == $name) {
        $this->childs[$i]->set_parent(new Null_Node());
        array_splice($this->childs, $i, 1);
        break;
      }
    }
  }

  public function get_childs() {
    return $this->childs;
  }

  public function set_parent($node) {
    $this->parent = $node;
  }

  public function get_parent() {
    return $this->parent;
  }

  /**
   * 액터의 소유자 명을 설정한다.
   *
   * @param owner Owner 소유자
   */
  public function set_owner($owner) {
    $this->owner = $owner;
  }

  public function get_owner() {
    return $this->owner;
  }


  /**
   * 액터의 접근권한을 설정한다.
   *
   * @param permission Permission 접근 권한
   */
  public function set_permission($permission) {
    $this->permission = $permission;
  } 

  public function get_permission() {
    return $this->permission;
  }

  public function release() {
    // 부모에서 나를 제거
    $this->parent->remove_child($this->get_name());
    // 나의 부모를 제거
    $this->set_parent(new Null_Node());
    // 내 자식들을 모두 삭제
    foreach ($this->childs as $child) {
      $child->release();
    }
    $this->childs = array();
  }

  public function update() {
    foreach ($this->childs as $child) {
      $child->update();
    }
  }

  public function is_null() {
    return false;
  }

  private $name;
  private $parent;
  private $childs;
  private $owner;
  private $permission;
}
