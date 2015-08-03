<?php
namespace af\plugins\sharding\dtos;

use af\plugins\sharding\adts\Null_Shard_Set_ID;

/**
 * @class Shard_Set_DTO
 *
 * @brief 
 * @author Lee, Hyeon-gi
 */
class Shard_Set_DTO {
  public function __construct() {
    $this->id = new Null_Shard_Set_ID();
  }
  //샤드셋 아이디  Shard_Set_ID
  public $id;
  // 테이블이 사용할 룩업 샤드 아이디 리스트 array(Shard_ID)
  public $lookup_shard_ids = array();
  // 테이블이 사용할 데이터 샤드 아이디 리스트 array(Shard_ID);
  public $data_shard_ids = array();
}
