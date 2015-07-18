<?php
namespace apdos\plugins\sharding\adts;

use apdos\kernel\objectid\ID;
use apdos\kernel\objectid\Object_ID;

/**
 * @class Shard_Object_ID
 *
 * @brieif 샤드간에 겹치지 않는 유니크 아이디 객체
 *         Timestamp(4byte) + Machine ID(3byte) + Process ID(2byte) + Increment count(2byte) + Lookup Shard ID(3byte)
 * @authoer Lee, Hyeon-gi
 */
class Shard_Object_ID extends Object_ID {
  const LOOKUP_SHARD_ID_SIZE = 3;

  public function __construct() {
  }

  public function init($lookup_shard_id, $current_time = -1, $max_generate_count = ID::MAX_GENERATE_COUNT_PER_SEC) {
    $this->lookup_shard_id = $lookup_shard_id;
    parent::init($current_time, $max_generate_count);
  }

  // @override 
  protected function pack_segments() {
    $this->binary = ID::get_instance()->generate_id($this->current_time, $this->max_generate_count);
    $this->binary .= $this->lookup_shard_id->to_string_hash(self::LOOKUP_SHARD_ID_SIZE);
  }

  // @override 
  public function get_lookup_shard_id_segment() {
    $this->unpack_segments();
    return $this->lookup_shard_id_segment;
  }

  protected function unpack_segments() {
    if (!$this->unpacked) {
      $unpack = ID::get_instance()->unpack($this->binary);
      $this->timestamp_segment = $unpack['timestamp_segment'];
      $this->machine_id_segment = $unpack['machine_id_segment'];
      $this->process_id_segment = $unpack['process_id_segment'];
      $this->increment_count_segment = $unpack['increment_count_segment'];
     
      $offset = strlen($this->binary) - self::LOOKUP_SHARD_ID_SIZE; 
      $this->lookup_shard_id_segment = substr($this->binary, $offset, self::LOOKUP_SHARD_ID_SIZE);

      $this->unpacked != $this->unpacked;
    }
  }

  /**
   * Shard_Object_ID를 생성한다.
   *
   * @param lookup_shard_id Shard_ID 룩업 샤드 아이디 객체
   * @param current_time int 유닉스 타임 스탬프
   * @param max_generate_count int 초당 생성할 수 있는 아이디 최대 갯수
   *
   * @return Shard_Object_ID
   */
  public static function create($lookup_shard_id, $current_time = -1, $max_generate_count = ID::MAX_GENERATE_COUNT_PER_SEC) {
    $result = new Shard_Object_ID();
    $result->init($lookup_shard_id, $current_time, $max_generate_count);
    return $result;
  }

  private $lookup_shard_id;
  private $lookup_shard_id_segment;
}

