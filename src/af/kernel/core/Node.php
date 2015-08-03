<?php
namespace af\kernel\core;

use af\kernel\event\Event_Dispatcher;
use af\kernel\log\Logger;

abstract class Node extends Event_Dispatcher {

  abstract public function get_name();

  abstract public function get_path();

  abstract public function add_child($node);

  abstract public function find_child($name);

  abstract public function remove_child($name);

  abstract public function get_childs();

  abstract public function set_parent($node);

  abstract public function get_parent();

  abstract public function get_owner();

  abstract public function get_permission();

  abstract public function release();
}
