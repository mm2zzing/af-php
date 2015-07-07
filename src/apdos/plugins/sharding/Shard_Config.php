<?php
namespace apdos\plugins\sharding;

use apdos\kernel\core\Kernel;
use apdos\kernel\actor\Actor;
use apdos\kernel\actor\Component;
use apdos\plugins\database\connecters\mysql\MySQL_Connecter;
use apdos\plugins\database\connecters\mysql\MySQL_Schema;
use apdos\plugins\database\connecters\mysql\MySQL_Util;
use apdos\plugins\sharding\dtos\DB_DTO;
use apdos\plugins\sharding\dtos\Shard_DTO;
use apdos\plugins\sharding\dtos\Table_DTO;
use apdos\plugins\sharding\adts\Table_ID;
use apdos\plugins\sharding\adts\Shard_ID;
use apdos\plugins\sharding\adts\Shard;
use apdos\plugins\sharding\adts\Null_Shard;
use apdos\plugins\sharding\adts\Table;
use apdos\plugins\sharding\adts\Null_Table;

class Shard_Config extends Component { 
  public function __construct() {
    $this->tables = array();
    $this->shards = array();
  } 
  /**
   * 샤딩 설정을 로드한다.
   *
   * @param tables array(object) 샤딩 테이블 리스트
   * @param shards array(object) 샤드 셋 리스트
   */
  public function load($tables, $shards) {
    foreach ($tables as $table) {
      $dto = new Table_DTO();
      $dto->id = new Table_ID($table->id);
      foreach ($table->lookup_shard_ids as $id) {
        array_push($dto->lookup_shard_ids, new Shard_ID($id));
      }
      foreach ($table->data_shard_ids as $id) {
        array_push($dto->data_shard_ids, new Shard_ID($id));
      }
      array_push($this->tables, new Table($dto));
    }

    foreach ($shards as $shard) {
      $dto = new Shard_DTO();
      $dto->id = new Shard_ID($shard->id);
      $dto->master = new DB_DTO();
      $this->import_db_dto($dto->master, $shard->master);
      $dto->slave = new DB_DTO();
      $this->import_db_dto($dto->slave, $shard->slave);

      array_push($this->shards, new Shard($dto));
    }
  }

  private function import_db_dto($dto, $data) {
    $dto->connecter = $data->connecter;
    $dto->host = $data->host;
    $dto->port = $data->port;
    $dto->user = $data->user;
    $dto->password = $data->password;
    $dto->db_name = $data->db_name;
    $dto->charset = $data->charset;
    $dto->persistent = $data->persistent;
  }

  /**
   * 샤드 정보를 조회한다.
   *
   * @param shard_id Shard_ID 샤드아이디
   *
   * @return Shard
   */
  public function get_shard($shard_id) {
    foreach ($this->shards as $shard) {
      if ($shard->get_id()->equal($shard_id))
        return $shard;
    }
    return new Null_Shard();
  }

  public function get_shards() {
    return $this->shards;
  }

  /**
   * 테이을 정보를 조회한다.
   *
   * @param table_id Table_ID 테이블 아이디
   *
   * @rturn Table
   */
  public function get_table($table_id) {
    foreach ($this->tables as $table) {
      if ($table->get_id()->equal($table_id))
        return $table;
    }
    return new Null_Table();
  }

  public function get_tables() {
    return $this->tables;
  }
}


