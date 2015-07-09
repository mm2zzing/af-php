<?php
namespace apdos\plugins\database\base\rdb;

abstract class RDB_Result {
  abstract public function is_success();
  abstract public function get_rows_count();
  abstract public function get_rows();
}
 
