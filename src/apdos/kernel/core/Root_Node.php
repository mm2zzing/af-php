<?php
namespace apdos\kernel\core;

use apdos\kernel\event\Event_Dispatcher;
use apdos\kernel\core\Node;
use apdos\kernel\core\Null_Node;
use apdos\kernel\core\Kernel;

class Root_Node extends Node {
  private $name;
  private $parent;
  private $childs;

  public function __construct($args) {
    parent::__construct($args, array('', 'construct2'));
  }

  public function construct2($name, $path) {
    $this->name = $name;
    $this->parent = new Null_Node();
    $this->childs = array();
  }

  public function get_name() {
    return $this->name;
  }

  public function get_path() {
    $nodes = array();
    $current_node = $this;
    do {
      $parent_node = $current_node->get_parent();
      array_push($nodes, $current_node);
      $current_node = $parent_node;
    }
    while (!$parent_node->is_null());
    $nodes = array_reverse($nodes);
    $result = '';
    $last_index = count($nodes) - 1;
    for ($i = 0; $i < count($nodes); $i++) {
      $result .= $nodes[$i]->get_name();
      if ($nodes[$i]->get_name() != '/' && $i != $last_index)
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
}
