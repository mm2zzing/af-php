<?php
namespace apdos\plugins\sharding\adts;

class Null_Shard extends Shard {
  public function __construct() {
  }

  public function is_null() {
    return true;
  }
}
