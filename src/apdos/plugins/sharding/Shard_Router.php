<?php
namespace apdos\plugins\sharding;

use apdos\kernel\core\Kernel;
use apdos\kernel\actor\Actor;
use apdos\kernel\actor\Component; 
use apdos\kernel\actor\events\Component_Event; 
use apdos\plugins\database\connecters\mysql\MySQL_Session;
use apdos\plugins\database\connecters\mysql\MySQL_Connecter;
use apdos\plugins\database\connecters\mysql\MySQL_Schema;
use apdos\plugins\database\connecters\mysql\MySQL_Util;
use apdos\plugins\sharding\dtos\DB_DTO;
use apdos\plugins\sharding\dtos\Shard_DTO;
use apdos\plugins\sharding\errors\Sharding_Error;
use apdos\plugins\sharding\adts\Shard_Object_ID;

class Shard_Router extends Component { 
  public function __construct() {
  }

  public function select_database() {
    $shards = $this->get_config()->get_shards();
    foreach ($shards as $shard) {
      try {
        $connecter = $this->get_session()->get_db_connecter($shard->get_id(), true);
        $connecter->select_database($shard->get_master()->db_name);
        $connecter = $this->get_session()->get_db_connecter($shard->get_id(), false);
        $connecter->select_database($shard->get_slave()->db_name);
      }
      catch (RDB_Error $e) {
        throw new Sharding_Error($e->getMessage(), Sharding_Error::QUERY_FAILED);
      }
    }
  }

  /**
   * 테이블 아이디가 가져야할 테이블 정보를 가지고 있는지 조회
   *
   * @param table_id Table_ID 테이블 아이디
   *
   * @throw Sharding_Error
   */
  public function has_table($table_id) {
    $shard_ids = $this->get_shard_set($table_id)->get_data_shard_ids();
    foreach ($shard_ids as $id) {
      try {
        $connecter = $this->get_session()->get_db_connecter($id);
        if (!$connecter->has_table($table_id->to_string()))
          return false;
      }
      catch (RDB_Error $e) {
        throw new Sharding_Error($e->getMessage(), Sharding_Error::QUERY_FAILED);
      }
    }
    return true;
  }

  /**
   * 룩업 테이블 정보를 가지고 있는지 조회
   *
   * @throw Sharding_Error
   */
  public function has_lookup_table() {
    foreach ($this->get_config()->get_tables() as $table) {
      $shard_set = $this->get_config()->get_shard_set($table->get_shard_set_id());
      $lookup_shard_ids = $shard_set->get_lookup_shard_ids();
      foreach ($lookup_shard_ids as $shard_id_str) {
        try {
          $db_connecter = $this->get_session()->get_db_connecter($shard_id_str);
          if (!$db_connecter->has_table($table->get_id()->to_string()))
            return false;
        }
        catch (RDB_Error $e) {
          throw new Sharding_Error($e->getMessage(), Sharding_Error::QUERY_FAILED);
        }
      }
    }
    return true;
  } 

  /**
   * 샤드중 하나에 데이터를 추가한다.
   *
   * @param table_id Table_ID 테이블 아이디
   * @param data array(key=>value) 추가할 데이터셋
   *
   * @throw Sharding_Error
   */
  public function insert($table_id, $data) {
    $shard_set = $this->get_shard_set($table_id);
    $lookup_shard_id = $this->select_shard($shard_set->get_lookup_shard_ids());
    $data_shard_id = $this->select_shard($shard_set->get_data_shard_ids());
    try {
      $data['object_id'] = Shard_Object_ID::create($lookup_shard_id)->to_string();
      $db_connecter = $this->get_session()->get_db_connecter($data_shard_id);
      $db_connecter->insert($table_id->to_string(), $data);
    }
    catch (RDB_Error $e) {
      $message = 'Insert shard id is ' . $insert_shard_id->to_string() . ': ' . $e->getMessage();
      throw new Sharding_Error($message, Sharding_Error::QUERY_FAILED);
    }
  }

  private function select_shard($shard_ids) {
    $rand_index = rand(0, count($shard_ids) -1);
    return $shard_ids[$rand_index];
  }

  public function get($table_id, $master = true) {
    $results = array();
    foreach ($this->get_shard_set($table_id)->get_data_shard_ids() as $shard_id) {
      try {
        $db_connecter = $this->get_session()->get_db_connecter($shard_id, $master);
        $result = $db_connecter->get($table_id->to_string());
        array_push($results, $result);
      }
      catch (RDB_Error $e) {
        $message = 'Get failed. shard_id is ' . $shard_id->to_string() . ': ' . $e->getMessage();
        throw new Sharding_Error($message, Sharding_Error::QUERY_FAILED);
      }
    }
    return new Shard_Result($results);
  }

  public function get_where($table_id, $wheres, $limit = -1, $offset = -1) {
  }

  private function get_shard_set($table_id) {
    $shard_set = $this->get_config()->get_table_shard_set($table_id);
    if ($shard_set->is_null())
      throw new Sharding_Error('Shard set is null. table id is ' . $table_id->to_string(), 
                               Sharding_Error::CONFIG_FAILED);
    return $shard_set;
  }
  
  private function get_config() {
    $component = $this->get_component(Shard_Config::get_class_name());
    if ($component->is_null())
      throw new Sharding_Error("Shard_Config is null", Sharding_Error::COMPONENT_FAILED);
    return $component;
  }

  private function get_session() {
    $component = $this->get_component(Shard_Session::get_class_name());
    if ($component->is_null())
      throw new Sharding_Error("Shard_Session is null", Sharding_Error::COMPONENT_FAILED);
    return $component;
  }
}


