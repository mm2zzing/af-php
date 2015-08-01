<?php
namespace apdos\plugins\sharding\adts;

class Null_Table extends Table {
  public function __construct() {
  }

  public function is_null() {
    return true;
  }

  public function get_id() {
    return new Null_Table_ID();
  }

  public function get_shard_set_id() {
    return new Null_Shard_Set_ID();
  }

}
