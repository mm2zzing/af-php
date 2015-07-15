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
use apdos\plugins\sharding\dtos\Shard_Set_DTO;
use apdos\plugins\sharding\dtos\Table_DTO;
use apdos\plugins\sharding\adts\Table_ID;
use apdos\plugins\sharding\adts\Shard_ID;
use apdos\plugins\sharding\adts\Shard_Set_ID;
use apdos\plugins\sharding\adts\Shard;
use apdos\plugins\sharding\adts\Null_Shard;
use apdos\plugins\sharding\adts\Table;
use apdos\plugins\sharding\adts\Null_Table;
use apdos\plugins\sharding\adts\Shard_Set;
use apdos\plugins\sharding\adts\Null_Shard_Set;

class Shard_Config extends Component { 
  public function __construct() {
    $this->tables = array();
    $this->shard_sets = array();
    $this->shards = array();
  } 
  /**
   * 샤딩 설정을 로드한다.
   *
   * @param tables array(object) 샤딩 테이블 리스트
   * @param lookup_shards array(object) 룩업 샤드 리스트
   * @param data_shards array(object) 데이터 샤드 리스트
   */
  public function load($tables, $shard_sets, $shards) {
    foreach ($tables as $table) {
      $dto = new Table_DTO();
      $dto->id = new Table_ID($table->id);
      $dto->shard_set_id = new Shard_Set_ID($table->shard_set_id);
      array_push($this->tables, new Table($dto));
    }
    foreach ($shard_sets as $shard_set) {
      $dto = $this->create_shard_set_dto($shard_set);
      array_push($this->shard_sets, new Shard_Set($dto));
    }

    foreach ($shards as $shard) {
      $dto = $this->create_shard_dto($shard);
      array_push($this->shards, new Shard($dto));
    }
  }

  private function create_shard_set_dto($shard_set) {
    $dto = new Shard_Set_DTO();
    $dto->id = new Shard_Set_ID($shard_set->id);
    foreach ($shard_set->lookup_shard_ids as $id) {
      array_push($dto->lookup_shard_ids, new Shard_ID($id));
    }
    foreach ($shard_set->data_shard_ids as $id) {
      array_push($dto->data_shard_ids, new Shard_ID($id));
    }
    return $dto;
  }

  private function create_shard_dto($shard) {
    $dto = new Shard_DTO();
    $dto->id = new Shard_ID($shard->id);
    $dto->master = new DB_DTO();
    $this->import_db_dto($dto->master, $shard->master);
    $dto->slave = new DB_DTO();
    $this->import_db_dto($dto->slave, $shard->slave);
    return $dto;
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

  /**
   * 샤드 셋 정보를 조회한다.
   *
   * @param shard_id Shard_ID 샤드아이디
   *
   * @return Shard
   */
  public function get_shard_set($shard_set_id) {
    foreach ($this->shard_sets as $shard_set) {
      if ($shard_set->get_id()->equal($shard_set_id))
        return $shard_set;
    }
    return new Null_Shard_Set();
  }

  public function get_shard_sets() {
    return $this->shard_sets;
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

  /**
   * 룩업 샤드를 조회
   *
   * @return array(Shard_ID)
   */
  public function get_lookup_shard_ids() {
    $shard_sets = $this->get_shard_sets();
    $ids = array();
    foreach ($shard_sets as $shard_set) {
      $shard_ids = $shard_set->get_lookup_shard_ids();
      foreach ($shard_ids as $id)
        array_push($ids, $id->to_string());
    }
    return $this->to_unique_shard_ids($ids);
  }

  public function get_data_shard_ids() {
    $shard_sets = $this->get_shard_sets();
    $ids = array();
    foreach ($shard_sets as $shard_set) {
      $shard_ids = $shard_set->get_data_shard_ids();
      foreach ($shard_ids as $id)
        array_push($ids, $id->to_string());
    }
    return $this->to_unique_shard_ids($ids);
  }

  private function to_unique_shard_ids($ids) {
    $ids = array_unique($ids);
    $shard_ids = array();
    foreach ($ids as $id) {
      array_push($shard_ids, new Shard_ID($id));
    }
    return $shard_ids;
  }

  public function get_table_shard_set($table_id) {
    $table = $this->get_table($table_id);
    return $this->get_shard_set($table->get_shard_set_id());
  }
}


