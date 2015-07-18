<?php
namespace apdos\kernel\objectid;

/**
 * @class Object_ID
 *
 * @brieif 머신, 프로세스간 겹치지 않는 유니크 아이디 객체
 *
 *         Timestamp(4byte) + Machine ID(3byte) + Process ID(2byte) + Increment count(2byte)
 * @authoer Lee, Hyeon-gi
 */ 
class Object_ID {
  public function __construct() {
  }

  public function init($current_time = -1, $max_generate_count = ID::MAX_GENERATE_COUNT_PER_SEC) {
    $this->current_time = $current_time;
    $this->max_generate_count = $max_generate_count;
    $this->pack_segments();
  }

  /**
   * 바이너리 형태로 아이디 필드 정보드를 팩한다
   *
   * @virtual
   */ 
  protected function pack_segments() {
    $this->binary = ID::get_instance()->generate_id($this->current_time, $this->max_generate_count);
  }

  /**
   * 원본 데이터 형태로  아이디 필드 정보드를 언팩한다
   *
   * @virtual
   */ 
  protected function unpack_segments() {
    if (!$this->unpacked) {
      $unpack = ID::get_instance()->unpack($this->binary);
      $this->timestamp_segment = $unpack['timestamp_segment'];
      $this->machine_id_segment = $unpack['machine_id_segment'];
      $this->process_id_segment = $unpack['process_id_segment'];
      $this->increment_count_segment = $unpack['increment_count_segment'];

      $this->unpacked != $this->unpacked;
    }
  }

  /**
   * 헥스 스트링을 통한 객체 초기화
   *
   * @hex string 헥스 스트링 데이터
   */
  public function init_by_string($hex) {
    $this->binary = '';
    for ($i=0; $i < strlen($hex) - 1; $i += 2) {
      $this->binary .= chr(hexdec($hex[$i].$hex[$i+1]));
    }
  }

  /**
   * 바이너리 데이터를 통한 객체 초기화
   *
   * @data string 바이너리 데이터
   */
  public function init_by_binary($data) {
    $this->binary = $data;
  }
 
  public function get_timestamp_segment() {
    $this->unpack_segments();
    return $this->timestamp_segment;
  }

  public function get_machine_id_segment() {
    $this->unpack_segments();
    return $this->machine_id_segment;
  }

  public function get_process_id_segment() {
    $this->unpack_segments();
    return $this->process_id_segment;
  }

  public function get_increment_count_segment() {
    $this->unpack_segments();
    return $this->increment_count_segment;
  }

  public function to_string() {
    $hex='';
    for ($i=0; $i < strlen($this->binary); $i++) {
      $hex .= dechex(ord($this->binary[$i]));
    }
    return $hex;
  }

  /**
   * Object_ID를 생성한다.
   *
   * @param current_time int 유닉스 타임 스탬프
   * @param max_generate_count int 초당 생성할 수 있는 아이디 최대 갯수
   *
   * @return Object_ID
   */
  public static function create($current_time = -1, $max_generate_count = ID::MAX_GENERATE_COUNT_PER_SEC) {
    $result = new Object_ID();
    $result->init($current_time, $max_generate_count);
    return $result;
  }

  protected $binary;
  protected $unpacked = false;
  protected $timestamp_segment;
  protected $machine_id_segment;
  protected $process_id_segment;
  protected $increment_count_segment;

  protected $current_time = 0;
  protected $max_generate_count = 0;
}
