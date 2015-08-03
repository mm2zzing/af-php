<?php
namespace af\plugins\database\base\rdb;

abstract class RDB_Result {
  abstract public function get_rows_count();
  abstract public function get_rows();
}
 
