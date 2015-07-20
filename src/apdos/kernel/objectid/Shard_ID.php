<?php
namespace apdos\kernel\objectid;

class Shard_ID {
  /**
   * Constructor
   *
   * @param id_string string 샤드 아이디 문자열
   */
  public function __construct($id_string) {
    $this->id = $id_string;
  }

  public function get_value() {
    return $this->id;
  }

  /**
   * 동등 비교
   *
   * @param shard_id Shard_ID 비교할 샤드 아이디
   */
  public function equal($shard_id) {
    return $this->get_value() == $shard_id->get_value() ? true : false;
  }

  public function to_string() {
    return $this->id;
  }

  /**
   * 문자열 타입으로 해시한 값을 되돌려준다.
   *
   * @param size string 해시 사이즈
   *
   * @return string 해시한 문자열
   */
  public function to_string_hash($size) {
    if (strlen($this->hash) != $size)
      $this->hash = substr(md5($this->id), 0, $size);
    return $this->hash;
  }

  private $id;
  private $hash = '';
}

