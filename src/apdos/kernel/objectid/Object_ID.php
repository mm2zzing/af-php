<?php
namespace apdos\kernel\objectid;

class Object_ID {
  public function __construct() {
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

  public function get_current_time() {
    $this->unpack_segments();
    return $this->current_time;
  }

  public function get_machine_id() {
    $this->unpack_segments();
    return $this->machine_id;
  }

  public function get_process_id() {
    $this->unpack_segments();
    return $this->process_id;
  }

  public function get_increment_count() {
    $this->unpack_segments();
    return $this->increment_count;
  }

  private function unpack_segments() {
    if (!$this->unpacked) {
      $offset = 0;
      $data = unpack(ID::ULONG_4BYTE_LE, substr($this->binary, $offset, ID::TIMESTAMPE_BYTE));
      $this->current_time = $data[1];
      $offset += ID::TIMESTAMPE_BYTE;

      $this->machine_id = substr($this->binary, $offset, ID::MACHINE_ID_BYTE);
      $offset += ID::MACHINE_ID_BYTE;

      $data = unpack(ID::USHORT_2BYTE_LE, substr($this->binary, $offset, ID::PROCESS_ID_BYTE));
      $this->process_id = $data[1];
      $offset += ID::PROCESS_ID_BYTE;

      $data = unpack(ID::USHORT_2BYTE_LE, substr($this->binary, $offset, ID::INCREMENT_COUNT_BYTE));
      $this->increment_count = $data[1];

      $this->unpacked != $this->unpacked;
    }
  }

  public function to_string() {
    $hex='';
    for ($i=0; $i < strlen($this->binary); $i++) {
      $hex .= dechex(ord($this->binary[$i]));
    }
    return $hex;
  }

  public static function create($current_time = -1, $max_generate_count = ID::MAX_GENERATE_COUNT_PER_SEC) {
    $binary = ID::get_instance()->generate_id($current_time, $max_generate_count);
    $result = new Object_ID();
    $result->init_by_binary($binary);
    return $result;
  }

  private $binary;
  private $unpacked = false;
  private $current_time;
  private $machine_id;
  private $process_id;
  private $increment_count;
}
