<?php
// @TODO Move to objectid plugin
namespace apdos\plugins\sharding\adts;

class Shard_ID {
  /** 
   * constructor
   * 
   * @param id_string string 아이디값
   */
  public function __construct($id) {
    $this->id = $id;
  }

  public function get_value() {
    return $this->id;
  }

  public function to_string() {
    return $this->id;
  }

  public function equal($other) {
    return $other->get_value() == $this->id ? true : false;
  }

  public function is_null() {
    return false;
  }

  private $id;
}
