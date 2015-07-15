<?php
namespace apdos\plugins\sharding\adts;

class Null_Shard_Set extends Shard_Set {
  public function __construct() {
  }

  public function is_null() {
    return true;
  }

  public function get_id() {
    return new Null_Shard_Set_ID();
  }

  public function get_lookup_shard_ids() {
    return array();
  }

  public function get_data_shard_ids() {
    return array();
  }
}
