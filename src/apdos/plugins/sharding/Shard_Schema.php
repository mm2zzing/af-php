<?php
namespace apdos\plugins\sharding;

use apdos\kernel\core\Kernel;
use apdos\kernel\actor\Component;
use apdos\plugins\sharding\Shard_Router;
use apdos\plugins\sharding\Shard_Config;

class Shard_Schema extends Component {
  private $session;

  public function __construct() {
  }

  public function create_databases() {
    $shards = $this->get_config()->get_shards();
    foreach ($shards as $shard) {
      $db_schema = $this->get_session()->get_db_schema($shard->get_id());
      if (!$db_schema->create_database($shard->get_master()->db_name))
        return false;
    }
    return true;
  }

  public function create_database($shard_id, $if_not_exists = true) {
    $shard = $this->get_config()->get_shard($shard_id);
    if ($shard->is_null())
      return false;
    $db_schema = $this->get_session()->get_db_schema($shard_id);
    return $db_schema->create_database($shard->get_master()->db_name, $if_not_exists);
  }

  public function drop_databases() {
    $shards = $this->get_config()->get_shards();
    foreach ($shards as $shard) {
      $db_schema = $this->get_session()->get_db_schema($shard->get_id());
      if (!$db_schema->drop_database($shard->get_master()->db_name))
        return false;
    }
    return true;
  }

  public function drop_database($shard_id, $if_exists = true) {
    $shard = $this->get_config()->get_shard($shard_id);
    $db_schema = $this->get_session()->get_db_schema($shard_id);
    return $db_schema->drop_database($shard->get_master->db_name, $if_exists);
  }

  /**
   * 샤드 여러곳에 테이블을 생성한다.
   *
   * @param shard_ids apdos\plugin\adts\Shard_IDs 샤드 아이디 콜렉션
   * @param name string 테이블명
   * @param fields array(key=>array(key=>value)) 필드 속성
   */
  public function create_tables($shard_ids, $name, $fields) {
    foreach ($shard_ids->gets() as $shard_id) {
      if (!$this->create_table($shard_id, $name, $fields))
        return false;
    }
    return true;
  }

  /**
   * 샤드 하나에 테이블을 생성한다.
   *
   * @param shard_ids apdos\plugin\adts\Shard_IDs 샤드 아이디 콜렉션
   * @param name string 테이블명
   * @param fields array(key=>array(key=>value)) 필드 속성
   */

  public function create_table($shard_id, $name, $fields) {
    $db_schema = $this->get_session()->get_db_schema($shard_id);
    return $db_schema->create_table($name, $fields);
  }

  public function drop_table($shard_id) {
    $db_schema = $this->get_session()->get_db_schema($shard_id);
    return $db_schema->drop_table($name, $fields);
  }

  private function get_session() {
    $result = $this->get_component(Shard_Session::get_class_name());
    if ($result->is_null())
      throw new \Exception("Shard_Session is null");
    return $result;
  }

  private function get_config() {
    $result = $this->get_component(Shard_Config::get_class_name());
    if ($result->is_null())
      throw new \Exception("Shard_Config is null");
    return $result;
  }
}
