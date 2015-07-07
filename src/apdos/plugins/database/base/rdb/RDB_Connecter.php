<?php
namespace apdos\plugins\database\base\rdb;

use apdos\kernel\actor\Component;

abstract class RDB_Connecter extends Component {
  abstract public function connect($host, $user, $password, $port, $is_persistent, $db_name);
  abstract public function close();
  abstract public function select_database($name);
  abstract public function has_table($name);

  abstract public function query($sql);
  abstract public function begin_trans();
  abstract public function end_trans();
  abstract public function get_last_error();
}
