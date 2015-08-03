<?php
namespace af\plugins\sharding\adts;

class Table {
  /**
   * Constructor
   *
   * @parma table_dto Table_DTO
   */
  public function __construct($table_dto) {
    $this->dto = $table_dto;
  }

  public function is_null() {
    return false;
  }

  public function get_id() {
    return $this->dto->id;
  }

  public function get_shard_set_id() {
    return $this->dto->shard_set_id;
  }

  private $dto;
}
