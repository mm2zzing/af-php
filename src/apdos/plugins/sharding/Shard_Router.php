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
    $shard_set = $this->get_config()->get_table_shard_set($table_id);
    if ($shard_set->is_null())
      throw new Sharding_Error('Shard set is null. table id is ' . $table_id->to_string(), Sharding_Error::CONFIG_FAILED);
    $shard_ids = $shard_set->get_data_shard_ids();
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
    $shard_ids = $this->get_config()->get_lookup_shard_ids();
    if (0 == count($shard_ids))
      throw new Sharding_Error('Lookup shard ids count is 0', Sharding_Error::CONFIG_FAILED);
    foreach ($shard_ids as $id) {
      try {
        $connecter = $this->get_session()->get_db_connecter($id);
        if (!$connecter->has_table('lookup'))
          return false;
      }
      catch (RDB_Error $e) {
        throw new Sharding_Error($e->getMessage(), Sharding_Error::QUERY_FAILED);
      }
    }
    return true;
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


