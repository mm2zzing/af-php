<?php
namespace af\plugins\sharding\adts;

class Shard {
  /**
   * Constructor
   *
   * @param shard_dto Shard_DTO 초기화할 샤드의 정보가 담긴 DTO
   */
  public function __construct($shard_dto) {
    $this->dto = $shard_dto;
  }

  public function is_null() {
    return false;
  }

  public function get_id() {
    return $this->dto->id;
  }

  public function get_master() {
    return $this->dto->master;
  }

  public function get_slave() {
    return $this->dto->slave;
  }

  private $dto;
}
