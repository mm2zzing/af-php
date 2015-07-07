<?php
namespace apdos\plugins\sharding\dtos;

use apdos\plugins\sharding\adts\Null_Table_ID;

/**
 * @class Table_DTO
 *
 * @brief 테이블이 사용할 샤딩 종류 설정
 * @author Lee, Hyeon-gi
 */
class Table_DTO {
  public function __construct() {
    $this->id = new Null_Table_ID();
  }
  // 테이블 아이디 Table_ID
  public $id;
  // 테이블이 사용할 룩업 샤드 아이디 리스트 array(Shard_ID)
  public $lookup_shard_ids = array();
  // 테이블이 사용할 데이터 샤드 아이디 리스트 array(Shard_ID);
  public $data_shard_ids = array();
}

//$id = new Null_Table_ID();
