<?php
namespace af\plugins\sharding\dtos;

use af\plugins\sharding\adts\Null_Table_ID;
use af\plugins\sharding\adts\Null_Shard_Set_ID;

/**
 * @class Table_DTO
 *
 * @brief 테이블이 사용할 샤딩 종류 설정
 * @author Lee, Hyeon-gi
 */
class Table_DTO {
  public function __construct() {
    $this->id = new Null_Table_ID();
    $this->shard_set_id = new Null_Shard_Set_ID();
  }
  // 테이블 아이디 Table_ID
  public $id;
  // 샤드세 아이디 Shard_Set_ID
  public $shard_set_id;
}
