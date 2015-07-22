<?php
namespace apdos\plugins\sharding\adts;

class Null_Table_ID extends Table_ID {
  public function __construct() {
    parent::__construct('');
  }

  public function is_null() {
    return true;
  }
}

