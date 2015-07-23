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
use apdos\kernel\objectid\Shard_ID;
use apdos\plugins\sharding\adts\Shard_Set_ID;
use apdos\plugins\sharding\adts\Shard;
use apdos\plugins\sharding\adts\Null_Shard;
use apdos\plugins\sharding\adts\Table;
use apdos\plugins\sharding\adts\Null_Table;
use apdos\plugins\sharding\adts\Shard_Set;
use apdos\plugins\sharding\adts\Null_Shard_Set;
use apdos\plugins\sharding\adts\Shard_Object_ID;
use apdos\plugins\sharding\errors\Shard_Error;

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
   *
   * @throw Shard_Error
   */
  public function load($tables, $shard_sets, $shards) {
    $this->load_tables($tables);
    $this->load_shard_sets($shard_sets); 
    $this->load_shards($shards); 
  }

  private function load_tables($tables) {
    $this->tables = array();
    foreach ($tables as $table) {
      $dto = new Table_DTO();
      $dto->id = Table_ID::create($table->id);
      $dto->shard_set_id = Shard_Set_ID::create($table->shard_set_id);
      array_push($this->tables, new Table($dto));
    }
    $this->validate_tables();
  }

  private function load_shard_sets($shard_sets) {
    $this->shard_sets = array();
    foreach ($shard_sets as $shard_set) {
      $dto = $this->create_shard_set_dto($shard_set);
      array_push($this->shard_sets, new Shard_Set($dto));
    }
    $this->validate_shard_sets();
  }

  private function load_shards($shards) {
    $this->shards = array();
    foreach ($shards as $shard) {
      $dto = $this->create_shard_dto($shard);
      array_push($this->shards, new Shard($dto));
    }
    $this->validate_shards();
  }

  private function validate_tables() {
    $ids = array();
    foreach ($this->tables as $table)
      array_push($ids, $table->get_id()->to_string());
    if (count($this->tables) != count(array_unique($ids)))
      throw new Shard_Error('Duplicated table id', Shard_Error::TABLE_ID_DUPLICATED);
  }


  private function validate_shards() {
    $ids = array();
    foreach ($this->shards as $shard)
      array_push($ids, $shard->get_id()->to_hash(Shard_ID::DEFAULT_HASH_SIZE));
    if (count($this->shards) != count(array_unique($ids)))
      throw new Shard_Error('Duplicated shard hash', Shard_Error::SHARD_HASH_DUPLICATED);
  }

  private function validate_shard_sets() {
    $ids = array();
    foreach ($this->shard_sets as $shard_set)
      array_push($ids, $shard_set->get_id()->to_string());
    if (count($this->shard_sets) != count(array_unique($ids)))
      throw new Shard_Error('Duplicated shard_set id', Shard_Error::SHARD_SET_ID_DUPLICATED);
  }
 
  private function create_shard_set_dto($shard_set) {
    $dto = new Shard_Set_DTO();
    $dto->id = Shard_Set_ID::create($shard_set->id);
    foreach ($shard_set->lookup_shard_ids as $id) {
      array_push($dto->lookup_shard_ids, Shard_ID::create($id));
    }
    foreach ($shard_set->data_shard_ids as $id) {
      array_push($dto->data_shard_ids, Shard_ID::create($id));
    }
    return $dto;
  }

  private function create_shard_dto($shard) {
    $dto = new Shard_DTO();
    if (isset($shard->hash))
      $dto->id = Shard_ID::create($shard->id, $shard->hash);
    else
      $dto->id = Shard_ID::create($shard->id);
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
   * 해시된 샤드 문자열을 이용하여 샤드 정보를 조회한다.
   *
   * @param shard_id_hash string shard_id_has 해시된 샤드 아이디a
   *
   * @returne Shard
   */
  public function get_shard_by_hash($shard_id_hash) {
    foreach ($this->shards as $shard) {
      if ($shard->get_id()->to_hash() == $shard_id_hash)
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
   * @param table_id string 테이블 아이디
   *
   * @rturn Table
   */
  public function get_table($table_id) {
    $table_id = Table_ID::create($table_id);
    foreach ($this->tables as $table) {
      if ($table->get_id()->equal($table_id))
        return $table;
    }
    return new Null_Table();
  }

  /**
   * 모든 테이블 정보를 조회한다.
   *
   * @return array(Table)
   */
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
      array_push($shard_ids, Shard_ID::create($id));
    }
    return $shard_ids;
  }

  public function get_table_shard_set($table_id) {
    $table_id = Table_ID::create($table_id);
    $table = $this->get_table($table_id->to_string());
    return $this->get_shard_set($table->get_shard_set_id());
  }
}


