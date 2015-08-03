<?php
namespace af\plugins\sharding\adts;

class Shard_Set {
  /**
   * Constructor
   *
   * @param shard_dto Shard_Set_DTO 초기화할 샤드의 정보가 담긴 DTO
   */
  public function __construct($shard_set_dto) {
    $this->dto = $shard_set_dto;
  }

  public function is_null() {
    return false;
  }

  public function get_id() {
    return $this->dto->id;
  }

  public function get_lookup_shard_ids() {
    return $this->dto->lookup_shard_ids;
  }

  public function get_data_shard_ids() {
    return $this->dto->data_shard_ids;
  }

  private $dto;
}
