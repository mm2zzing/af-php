<?php
namespace apdos\plugins\sharding\adts;

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

  private $dto;
}
