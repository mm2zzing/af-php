<?php
namespace apdos\plugins\sharding\adts;

use apdos\kernel\objectid\ID_Timestamp;
use apdos\kernel\objectid\ID;
use apdos\kernel\objectid\Shard_ID;
use apdos\kernel\core\Assert;

/**
 * @class Shard_Object_ID 
 * 
 * @brieif 샤드된 데이터의 유니크 아이디 클래스 
 *         머신, 프로세스, 룩업 샤드간 겹치지 않는다.
 * 
 *         Timestamp(4byte) + Machine ID(3byte) + Process ID(2byte) + Increment count(2byte) + Lookup Shard ID(3byte) 
 * @authoer Lee, Hyeon-gi
 */
class Shard_Object_ID extends ID {
  const ULONG_4BYTE_LE = "V";
  const USHORT_2BYTE_LE = "v";

  const TIMESTAMPE_BYTE = 4;
  const MACHINE_ID_BYTE = 3;
  const PROCESS_ID_BYTE = 2;
  const INCREMENT_COUNT_BYTE = 2;
  const TOTAL_BYTE = 14;

  public function __construct() {
  }

  public function init($lookup_shard_id, $current_time = -1, $max_generate_count = ID_Timestamp::MAX_GENERATE_COUNT_PER_SEC) {
    $timestamp = ID_Timestamp::get_instance()->generate($current_time, $max_generate_count);
    $this->binary = '';
    $this->binary .= pack(self::ULONG_4BYTE_LE, $timestamp['gen_timestamp']);
    $this->binary .= ID::create_hashed_machine_name(self::MACHINE_ID_BYTE);
    $this->binary .= pack(self::USHORT_2BYTE_LE, ID::create_process_id());
    $this->binary .= pack(self::USHORT_2BYTE_LE, $timestamp['gen_increment']);
    $this->binary .= $lookup_shard_id->to_hash(Shard_ID::DEFAULT_HASH_SIZE);
  }

  public function equal($id) {
    if ($id->get_class_name() != $this->get_class_name())
      return false;
    return $this->to_hash() == $shard_id->to_hash() ? true : false;
  }

  /** 
   * 헥스 스트링을 통한 객체 초기화 
   *
   * @hex string 헥스 스트링 데이터
   */
  public function init_by_string($hex_string) {
    $this->binary = hex2bin($hex_string);
  }

  public function to_string() {
    return bin2hex($this->binary);
  } 

  public function get_timestamp() {
    $this->unpacks();
    return $this->timestamp;
  }

  public function get_machine_id() {
    $this->unpacks();
    return $this->machine_id;
  }

  public function get_process_id() {
    $this->unpacks();
    return $this->process_id;
  }

  public function get_increment_count() {
    $this->unpacks();
    return $this->increment_count;
  }

  public function get_lookup_shard_id() {
    $this->unpacks();
    return $this->lookup_shard_id;
  }

  protected function unpacks() {
    if (!$this->unpacked) {
      ASSERT('strlen($this->binary) == self::TOTAL_BYTE');
      $result = array();
      $offset = 0;
      $data = unpack(self::ULONG_4BYTE_LE, substr($this->binary, $offset, self::TIMESTAMPE_BYTE));
      $this->timestamp = $data[1];
      $offset += self::TIMESTAMPE_BYTE;

      $this->machine_id = substr($this->binary, $offset, self::MACHINE_ID_BYTE);
      $offset += self::MACHINE_ID_BYTE;

      $data = unpack(self::USHORT_2BYTE_LE, substr($this->binary, $offset, self::PROCESS_ID_BYTE));
      $this->process_id = $data[1];
      $offset += self::PROCESS_ID_BYTE;

      $data = unpack(self::USHORT_2BYTE_LE, substr($this->binary, $offset, self::INCREMENT_COUNT_BYTE));
      $this->increment_count = $data[1];
      $offset += self::INCREMENT_COUNT_BYTE;
      $this->lookup_shard_id = substr($this->binary, $offset, Shard_ID::DEFAULT_HASH_SIZE);
      $this->unpacked != $this->unpacked;
    }
  }

  private $unpacked = false;
  private $timestamp;
  private $machine_id;
  private $process_id;
  private $increment_count;
  private $lookup_shard_id;

  /**
   * Shard_Object_ID를 생성한다.
   *
   * @param lookup_shard_id Shard_ID 룩업 샤드 아이디 객체
   * @param current_time int 유닉스 타임 스탬프
   * @param max_generate_count int 초당 생성할 수 있는 아이디 최대 갯수
   *
   * @return Shard_Object_ID
   */
  public static function create($lookup_shard_id, $current_time = -1, $max_generate_count = ID_Timestamp::MAX_GENERATE_COUNT_PER_SEC) {
    $result = new Shard_Object_ID();
    $result->init($lookup_shard_id, $current_time, $max_generate_count);
    return $result;
  }

  public static function create_by_string($hex_string) {
    $result = new Shard_Object_ID();
    $result->init_by_string($string);
    return $result;
  }
}

