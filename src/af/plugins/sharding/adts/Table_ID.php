<?php
namespace af\plugins\sharding\adts;

use af\kernel\objectid\ID;

class Table_ID extends ID {
  public function __construct() {
  }

  /** 
   * 
   * @param id_string string 아이디값
   */
  public function init_by_string($id) {
    $this->binary = $id;
  }

  public function to_string() {
    return $this->binary;
  }

  public function is_null() {
    return false;
  }

  static public function create($id_string) {
    $id = new Table_ID();
    $id->init_by_string($id_string);
    return $id;
  }
}
