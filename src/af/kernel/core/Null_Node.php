<?php
namespace af\kernel\core;

use af\kernel\core\Node;

class Null_Node extends Node {
  public function __construct() { }

  public function is_null() {
    return true;
  }

  public function get_name() {
    return '';
  }

  public function get_path() {
    return '';
  }

  public function add_child($node) {
  }

  public function find_child($name) {
  }

  public function remove_child($name) {
  }

  public function get_childs() {
    return array();
  }

  public function set_parent($node) {
  }

  public function get_parent() {
    return new Null_Node();
  }

  public function get_owner() {
    echo 'nullowner';
    return '';
  }

  public function get_permission() {
    return '------';
  }

  public function release() {
  }
}
