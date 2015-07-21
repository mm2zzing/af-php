<?php
namespace apdos\plugins\sharding;

use apdos\kernel\core\Kernel;
use apdos\kernel\actor\Component;
use apdos\plugins\sharding\Shard_Router;
use apdos\plugins\sharding\errors\Shard_Error;
use apdos\plugins\sharding\Shard_Config;
use apdos\kernel\objectid\Shard_ID;
use apdos\kernel\log\Logger;
use apdos\plugins\database\base\rdb\errors\RDB_Error;

class Shard_Schema extends Component {
  private $session;

  public function __construct() {
  }

  public function create_database($if_not_exists = true) {
    $shards = $this->get_config()->get_shards();
    foreach ($shards as $shard) {
      try {
        $db_schema = $this->get_session()->get_db_schema($shard->get_id());
        $db_schema->create_database($shard->get_master()->db_name, $if_not_exists);
        $db_connecter = $this->get_session()->get_db_connecter($shard->get_id());
        $db_connecter->select_database($shard->get_master()->db_name);
      }
      catch (RDB_Error $e) {
        $message = 'Create database failed. shard id ' . $shard->get_id()->to_string();
        throw new Shard_Error($message, Shard_Error::QUERY_FAILED);
      }
    }
  }

  public function has_database() {
    $shards = $this->get_config()->get_shards();
    foreach ($shards as $shard) {
      $db_schema = $this->get_session()->get_db_schema($shard->get_id());
      if (!$db_schema->has_database($shard->get_master()->db_name))
        return false;
    }
    return true;
  }

  public function drop_database($if_exists = true) {
    $shards = $this->get_config()->get_shards();
    foreach ($shards as $shard) {
      try {
        $db_schema = $this->get_session()->get_db_schema($shard->get_id());
        $db_schema->drop_database($shard->get_master()->db_name, $if_exists); 
      }
      catch (RDB_Error $e) {
        $message = 'Drop database failed. shard id ' . $shard->get_id()->to_string();
        throw new Shard_Error($message, Shard_Error::QUERY_FAILED);
      }
    }
  }

  public function create_lookup_table() {
    foreach ($this->get_config()->get_tables() as $table) {
      $shard_set = $this->get_config()->get_shard_set($table->get_shard_set_id());
      $lookup_shard_ids = $shard_set->get_lookup_shard_ids();
      foreach ($lookup_shard_ids as $shard_id_str) {
        try {
          $db_schema = $this->get_session()->get_db_schema($shard_id_str);
          $db_schema->create_table($table->get_id()->to_string(), $this->get_lookup_fields()); 
        }
        catch (RDB_Error $e) {
          $message = 'Create lookup table failed. shard id ' . $shard_id_str;
          throw new Shard_Error($message, Shard_Error::QUERY_FAILED);
        }
      }
    }
  }

  public function drop_lookup_table($if_exists = true) {
    foreach ($this->get_config()->get_tables() as $table) {
      $shard_set = $this->get_config()->get_shard_set($table->get_shard_set_id());
      $lookup_shard_ids = $shard_set->get_lookup_shard_ids();
      foreach ($lookup_shard_ids as $shard_id_str) {
        try {
          $db_schema = $this->get_session()->get_db_schema($shard_id_str);
          $db_schema->drop_table($table->get_id()->to_string()); 
        }
        catch (RDB_Error $e) {
          $message = 'Drop lookup table failed. shard id ' . $shard_id_str;
          throw new Shard_Error($message, Shard_Error::QUERY_FAILED);

        }
      }
    }
  }

  /**
   * Sharding Table에 사용되는 샤드에 룩업테이블과 데이터 테이블을 생성한다.
   *
   * @param table_id Table_ID 샤딩 테이블 아이디
   * @param data_fields array(key=>array(key=>value)) 데이터 샤드 테이블의 필드 속성
   *
   * @throw Shard_Error 요청을 한 테이블 아이디 / 샤드 셋 정보가 설정에 존재하지 않는 경우 예외 발생
   */
  public function create_table($table_id, $data_fields) {
    $data_shard_ids = $this->get_config()->get_table_shard_set($table_id)->get_data_shard_ids();
    foreach ($data_shard_ids as $shard_id) {
      try {
        $db_schema = $this->get_session()->get_db_schema($shard_id);
        $fields = array_merge($data_fields, $this->get_object_id_fields());
        $db_schema->create_table($table_id->to_string(), $fields);
      }
      catch (RDB_Error $e) {
        $message = 'Create data table failed. shard id ' . $shard_id->to_string();
        throw new Shard_Error($message, Shard_Error::QUERY_FAILED);
      }
    }
  }

  public function drop_table($table_id, $if_exists = true) {
    $shard_set = $this->get_config()->get_table_shard_set($table_id);

    $lookup_shard_ids = $shard_set->get_lookup_shard_ids();
    foreach ($lookup_shard_ids as $shard_id) {
      try {
        $db_schema = $this->get_session()->get_db_schema($shard_id);
        $db_schema->drop_table($table_id->to_string());
      }
      catch (RDB_Error $e) {
        $message = 'Delete lookup row failed. shard id ' . $shard_id->to_string();
        throw new Shard_Error($message, Shard_Error::QUERY_FAILED);
      }
    }

    $data_shard_ids = $shard_set->get_data_shard_ids();
    foreach ($data_shard_ids as $shard_id) {
      try {
        $db_schema = $this->get_session()->get_db_schema($shard_id);
        $db_schema->drop_table($table_id->to_string(), $if_exists);
      }
      catch (RDB_Error $e) {
        $message = 'Drop data table failed. shard id ' . $shard_id->to_string();
        throw new Shard_Error($message, Shard_Error::QUERY_FAILED);
      }
    }
    return true;
  }

  private function get_lookup_fields() {
    return array(
      'object_id'=>array(
        'type'=>'VARCHAR(100)',
        'null'=>FALSE,
        'default'=>''
      ),
      'data_shard_id'=>array(
        'type'=>'VARCHAR(100)',
        'null'=>FALSE,
        'default'=>''
      ),
      'state'=>array(
        'type'=>'VARCHAR(100)',
        'null'=>FALSE,
        'default'=>''
      )
    );
  }

  private function get_object_id_fields() {
    return array(
      'object_id'=>array(
        'type'=>'VARCHAR(100)',
        'null'=>FALSE,
        'default'=>''
      )
    );
  }
 
  private function get_session() {
    $result = $this->get_component(Shard_Session::get_class_name());
    if ($result->is_null())
      throw new Shard_Error("Shard_Session is null", Shard_Error::COMPONENT_FAILED);
    return $result;
  }

  private function get_config() {
    $result = $this->get_component(Shard_Config::get_class_name());
    if ($result->is_null())
      throw new Shard_Error("Shard_Config is null", Shard_Error::COMPONENT_FAILED);
    return $result;
  }
}
