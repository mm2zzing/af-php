<?php
namespace apdos\plugins\sharding\adts;

class Null_Shard_ID extends Shard_ID {
  public function __construct() {
    parent::__construct('');
  }

  public function is_null() {
    return true;
  }
}

