<?php
namespace apdos\kernel\objectid;

/**
 * @class Shard_ID 
 *
 * @brief 샤드 머신간의 구별을 위한 ID
 * @author Lee, Hyeon-gi
 */
class Shard_ID extends ID {
  const DEFAULT_HASH_SIZE = 3;

  /**
   * Constructor
   *
   * @param id_string string 샤드 아이디 문자열
   */
  public function __construct() {
  }

  public function init($id_string, $static_hash = '') {
    $this->binary = $id_string;
    $this->static_hash = $static_hash;
  }

  public function init_by_string($data) {
    $this->binary = $data;
  }
 
  public function to_string() {
    return $this->get_value();
  }

  public function to_hash($size = self::DEFAULT_HASH_SIZE) {
    if (strlen($this->static_hash))
      return substr($this->static_hash, 0, $size);
    else
      return substr(md5($this->binary), 0, $size);
  }

  static public function create($id_string, $static_hash = '') {
    $id = new Shard_ID();
    $id->init($id_string, $static_hash);
    return $id;
  }

  static public function create_by_string($id_string) {
    $id = new Shard_ID();
    $id->init_by_string($id_string);
    return $id;
  }

  private $static_hash = '';
}

