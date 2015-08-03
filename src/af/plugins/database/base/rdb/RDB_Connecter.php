<?php
namespace af\plugins\database\base\rdb;

use af\kernel\actor\Component;

abstract class RDB_Connecter extends Component {
  abstract public function connect($host, $user, $password, $port, $is_persistent, $db_name);
  abstract public function close();
  abstract public function select_database($name);
  abstract public function has_table($name);

  abstract public function query($sql);
  abstract public function insert($table_name, $data);
  abstract public function insert_batch($table_name, $data);

  abstract public function get($table_name, $limit = -1, $offset = -1);
  abstract public function get_where($table_name, $wheres, $limit = -1, $offset = -1);
  abstract public function limit($limit, $offset);
  abstract public function select($select_fields);
  abstract public function select_max($select_fields);
  abstract public function select_min($select_fields);
  abstract public function select_avg($select_fields);
  abstract public function select_sum($select_fields);
  abstract public function count($table_name);
  abstract public function delete($table_name, $wheres);
  abstract public function delete_all($table_name);

  abstract public function join($table_name, $condition);

  abstract public function order_by_asc($field_name);
  abstract public function order_by_desc($field_name);
  abstract public function update($table_name, $update_fields);

  abstract public function begin_trans();
  abstract public function end_trans();
  abstract public function get_last_error();

  abstract public function toggle_escape_query($enable);
}
