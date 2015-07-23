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
use apdos\plugins\sharding\errors\Shard_Error;
use apdos\plugins\sharding\adts\Shard_Object_ID;
use apdos\plugins\database\base\rdb\errors\RDB_Error;
use apdos\kernel\objectid\Shard_ID;

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
        throw new Shard_Error($e->getMessage(), Shard_Error::QUERY_FAILED);
      }
    }
  }

  /**
   * 테이블 아이디가 가져야할 테이블 정보를 가지고 있는지 조회
   *
   * @param table_id Table_ID 테이블 아이디
   *
   * @throw Shard_Error
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
        throw new Shard_Error($e->getMessage(), Shard_Error::QUERY_FAILED);
      }
    }
    return true;
  }

  /**
   * 룩업 테이블 정보를 가지고 있는지 조회
   *
   * @throw Shard_Error
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
          throw new Shard_Error($e->getMessage(), Shard_Error::QUERY_FAILED);
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
   * @return Shard_Object_ID 추가한 데이터의 object_id
   * @throw Shard_Error
   */
  public function insert($table_id, $data) {
    $shard_set = $this->get_shard_set($table_id);
    $lookup_shard_id = $this->select_shard($shard_set->get_lookup_shard_ids());
    $data_shard_id = $this->select_shard($shard_set->get_data_shard_ids());
    try {
      $object_id = Shard_Object_ID::create($lookup_shard_id);
      $object_id_str = $object_id->to_string();

      $lookup_shard_data = array('object_id'=>$object_id_str, 'data_shard_id'=>$data_shard_id->to_string());
      $db_connecter = $this->get_session()->get_db_connecter($lookup_shard_id);
      $db_connecter->insert($table_id->to_string(), $lookup_shard_data);

      $data_shard_data = array_merge($data, array('object_id'=>$object_id_str));
      $db_connecter = $this->get_session()->get_db_connecter($data_shard_id);
      $db_connecter->insert($table_id->to_string(), $data_shard_data);
      return $object_id;
    }
    catch (RDB_Error $e) {
      $message = 'Insert shard id is ' . $insert_shard_id->to_string() . ': ' . $e->getMessage();
      throw new Shard_Error($message, Shard_Error::QUERY_FAILED);
    }
  }

  private function select_shard($shard_ids) {
    $rand_index = rand(0, count($shard_ids) -1);
    return $shard_ids[$rand_index];
  }

  public function get($table_id, $master = true) {
    $results = array();
    foreach ($this->get_target_shard_ids($table_id) as $shard_id) {
      try {
        $db_connecter = $this->get_session()->get_db_connecter($shard_id, $master);
        if ($this->limit_per_shard != -1 && $this->offset_per_shard != -1)
          $db_connecter->limit($this->limit_per_shard, $this->offset_per_shard);
        if (count($this->select_fields))
          $db_connecter->select($this->select_fields);
        $result = $db_connecter->get($table_id->to_string());
        array_push($results, $result);
      }
      catch (RDB_Error $e) {
        $this->reset_query();
        $message = 'Get failed. shard_id is ' . $shard_id->to_string() . ': ' . $e->getMessage();
        throw new Shard_Error($message, Shard_Error::QUERY_FAILED);
      }
    }
    $this->reset_query();
    return new Shard_Result($results);
  }

  public function get_where($table_id, $wheres) {
    if (isset($wheres['object_id'])) {
      $data_shard_id = $this->get_data_shard_id($table_id, $wheres['object_id']);
      $results = array($this->get_where_from_shard($data_shard_id, $table_id, $wheres));
    }
    else {
      $results = array();
      foreach ($this->get_target_shard_ids($table_id) as $shard_id) {
        array_push($results, $this->get_where_from_shard($shard_id, $table_id, $wheres));
      }
    }
    $this->reset_query();
    return new Shard_Result($results);
  }

  private function get_data_shard_id($table_id, $object_id_str) {
    $object_id = Shard_Object_ID::create_by_string($object_id_str);
    $shard = $this->get_config()->get_shard_by_hash($object_id->get_lookup_shard_id());
    try {
      $result = $this->get_session()->get_db_connecter($shard->get_id())->get_where(
        $table_id->to_string(),
        array('object_id'=>$object_id_str));
      if (0 == $result->get_rows_count()) {
        throw new Shard_Error('Lookup data is null. object id is ' . $object_id_str, Shard_Error::LOOKUP_DATA_IS_NULL);
      }
    }
    catch (RDB_Error $e) {
      $message = 'Lookup data find failed. lookup shard is ' . $shard->get_id()->to_string() . ': ' . $e->getMessage();
        throw new Shard_Error($message, Shard_Error::QUERY_FAILED);
    }
    return Shard_ID::create_by_string($result->get_row(0, 'data_shard_id'));
  }

  private function get_where_from_shard($shard_id, $table_id, $wheres) {
    try {
      $db_connecter = $this->get_session()->get_db_connecter($shard_id);
      if ($this->limit_per_shard != -1 && $this->offset_per_shard != -1)
        $db_connecter->limit($this->limit_per_shard, $this->offset_per_shard);
      if (count($this->select_fields))
        $db_connecter->select($this->select_fields);
      return $db_connecter->get_where($table_id->to_string(), $wheres);
    }
    catch (RDB_Error $e) {
      $this->reset_query();
      $message = 'Get where failed. shard_id is ' . $shard_id->to_string() . ': ' . $e->getMessage();
      throw new Shard_Error($message, Shard_Error::QUERY_FAILED);
    }
  }

  private function get_shard_set($table_id) {
    $shard_set = $this->get_config()->get_table_shard_set($table_id);
    if ($shard_set->is_null())
      throw new Shard_Error('Shard set is null. table id is ' . $table_id->to_string(), 
                               Shard_Error::CONFIG_FAILED);
    return $shard_set;
  }

  public function update($table_id, $values) {
    $results = array();
    foreach ($this->get_target_shard_ids($table_id) as $shard_id) {
      try {
        array_push($results, $this->get_session()->get_db_connecter($shard_id)->update($table_id->to_string(), $values));
      }
      catch (RDB_Error $e) {
        $message = 'Update failed. shard_id is ' . $shard_id->to_string() . ': ' . $e->getMessage();
        throw new Shard_Error($message, Shard_Error::QUERY_FAILED);
      }
    }
    return new Shard_Result($results);
  }

  public function update_where($table_id, $values, $wheres) {
    $results = array();
    if (isset($wheres['object_id'])) {
      $data_shard_id = $this->get_data_shard_id($table_id, $wheres['object_id']);
      $results = array($this->update_where_from_shard($data_shard_id, $table_id, $values, $wheres));
    }
    else {
      foreach ($this->get_target_shard_ids($table_id) as $shard_id) {
        array_push($results, $this->update_where_from_shard($shard_id, $table_id, $values, $wheres));
      }
    }
    return new Shard_Result($results);
  }

  private function update_where_from_shard($shard_id, $table_id, $values, $wheres) {
    try {
      $db_connecter = $this->get_session()->get_db_connecter($shard_id);
      $result = $db_connecter->update_where($table_id->to_string(), $values, $wheres);
      array_push($results, $result);
    }
    catch (RDB_Error $e) {
      $message = 'Update where failed. shard_id is ' . $shard_id->to_string() . ': ' . $e->getMessage();
      throw new Shard_Error($message, Shard_Error::QUERY_FAILED);
    }
    return $result;
  }

  public function delete_all($table_id) {
    $results = array();
    foreach ($this->get_target_shard_ids($table_id) as $shard_id) {
      try {
        $db_connecter = $this->get_session()->get_db_connecter($shard_id);
        array_push($results, $db_connecter->delete_all($table_id->to_string()));
      }
      catch (RDB_Error $e) {
        $message = 'Delete all failed. shard_id is ' . $shard_id->to_string() . ': ' . $e->getMessage();
        throw new Shard_Error($message, Shard_Error::QUERY_FAILED);
      }
    }
    return new Shard_Result($results);
  }

  public function delete($table_id, $wheres) {
    $results = array();
    if (isset($wheres['object_id'])) {
      $data_shard_id = $this->get_data_shard_id($table_id, $wheres['object_id']);
      $results = array($this->delete_from_shard($data_shard_id, $table_id, $wheres));
    }
    else {
      $results = array();
    }
    return new Shard_Result($results);
  }

  private function delete_from_shard($shard_id, $table_id, $wheres) {
    try {
      $db_connecter = $this->get_session()->get_db_connecter($shard_id);
      return $db_connecter->delete($table_id->to_string(), $wheres);
    }
    catch (RDB_Error $e) {
      $message = 'Delete all failed. shard_id is ' . $shard_id->to_string() . ': ' . $e->getMessage();
      throw new Shard_Error($message, Shard_Error::QUERY_FAILED);
    }
    return $results;
  }

  public function count($table_id) {
    $count = 0;
    foreach ($this->get_target_shard_ids($table_id) as $shard_id) {
      try {
        $db_connecter = $this->get_session()->get_db_connecter($shard_id);
        $count += $db_connecter->count($table_id->to_string());
      } 
      catch (RDB_Error $e) {
        $message = 'Count failed. shard_id is ' . $shard_id->to_string() . ': ' . $e->getMessage();
        throw new Shard_Error($message, Shard_Error::QUERY_FAILED);
      }
    }
    return $count;
  }

  /**
   * 각 샤드에서 가져올 데이터의 갯수를 제한하는 메서드
   *
   * @parem limit int  가져올 갯수
   * @param offset int 가져올 오프셋 인덱스
   */
  public function slimit($limit, $offset) {
    $this->limit_per_shard = $limit;
    $this->offset_per_shard = $offset;
    return $this;
  }

  public function sselect($fields) {
    $this->select_fields = $fields;
    return $this;
  }

  /**
   * 데이터 조회를 처리할 샤드의 갯수를 제한한다.
   *
   * @param limit int 제한할 샤드 수
   * @param offset int 제한할 샤드의 오프셋 인덱스
   * @param random bool 처리할 샤드를 랜덤하게 선택할지 여부. 
                        래덤이 아닌 경우 Shard_Config에 추가되어 있는
                        순서를 기준으로 제한한다.
   */
  public function filter($limit_count, $limit_offset = 0, $random = true) {
    $this->shard_limit_count = $limit_count;
    $this->shard_limit_offset = $limit_offset;
    $this->shard_limit_is_random = $random;
    return $this;
  }

  public function reset_query() {
    $this->limit_per_shard = -1;
    $this->offset_per_shard = -1;
    $this->select_fields = array();
    $this->shard_limit_count = -1;
    $this->shard_limit_offset = -1;
    $this->shard_limit_is_random = false;
  }

  private function get_target_shard_ids($table_id) {
    $ids = $this->get_shard_set($table_id)->get_data_shard_ids();
    if ($this->shard_limit_is_random)
      shuffle($ids);
    if ($this->shard_limit_count != -1 && $this->shard_limit_offset != -1)
      return array_slice($ids, $this->shard_limit_offset, $this->shard_limit_count);
    else
      return $ids;
  }

  private $limit_per_shard = -1;
  private $offset_per_shard = -1;
  private $select_fields = array();
  private $shard_limit_count = -1;
  private $shard_limit_offset = -1;
  private $shard_limit_is_random = false;
  
  private function get_config() {
    $component = $this->get_component(Shard_Config::get_class_name());
    if ($component->is_null())
      throw new Shard_Error("Shard_Config is null", Shard_Error::COMPONENT_FAILED);
    return $component;
  }

  private function get_session() {
    $component = $this->get_component(Shard_Session::get_class_name());
    if ($component->is_null())
      throw new Shard_Error("Shard_Session is null", Shard_Error::COMPONENT_FAILED);
    return $component;
  }
}


