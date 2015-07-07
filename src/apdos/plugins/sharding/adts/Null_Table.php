<?php
namespace apdos\plugins\sharding\adts;

class Null_Table extends Table {
  public function __construct() {
  }

  public function is_null() {
    return true;
  }
}
