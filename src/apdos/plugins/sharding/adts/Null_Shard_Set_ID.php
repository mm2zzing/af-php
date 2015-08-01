<?php
namespace apdos\plugins\sharding\adts;

class Null_Shard_Set_ID extends Shard_Set_ID {
  public function __construct() {
    parent::__construct('');
  }

  public function is_null() {
    return true;
  }
}

