<?php
namespace apdos\kernel\core;

use apdos\kernel\event\Event_Dispatcher;
use apdos\kernel\log\Logger;

class Node extends Event_Dispatcher {

  public function __construct() {
  }

  public function get_name() {
    Logger::get_instance()->error('Node', 'node is inteface');
  }

  public function get_path() {
    Logger::get_instance()->error('Node', 'node is inteface');
  }

  public function add_child($node) {
    Logger::get_instance()->error('Node', 'node is inteface');
  }

  public function find_child($name) {
    Logger::get_instance()->error('Node', 'node is inteface');
  }

  public function remove_child($name) {
    Logger::get_instance()->error('Node', 'node is inteface');
  }

  public function get_childs() {
    Logger::get_instance()->error('Node', 'node is inteface');
  }

  public function set_parent($node) {
    Logger::get_instance()->error('Node', 'node is inteface');
  }

  public function get_parent() {
    Logger::get_instance()->error('Node', 'node is inteface');
  }

  public function release() {
    Logger::get_instance()->error('Node', 'node is inteface');
  }
}
