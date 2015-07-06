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

class Shard_Router extends Component { 
  public function __construct() {
    $this->tables = array();
    $this->shards = array();
  }

  /**
   * 샤딩 설정을 로드한다.
   *
   * @param shard_tables array(object) 샤딩 테이블 리스트
   * @param shard_sets array(object) 샤드 셋 리스트
   */
  public function load($shard_tables, $shard_sets) {
    $dto = new DB_DTO();
    $dto->host = 'localhost';
    $dto->port = '3306';
    $dto->user = 'root';
    $dto->password = '';
    $dto->db_name = '';
    $dto->charset = 'utf8';
    $dto->persistent = true;

    $shard_dto = new Shard_DTO();
    $shard_dto->id = 'lookup01';
    $shard_dto->master = $dto;
    $shard_dto->slave = $dto;

    $this->create_shard_db($shard_dto);

    $shard_dto = new Shard_DTO();
    $shard_dto->id = 'lookup02';
    $shard_dto->master = $dto;
    $shard_dto->slave = $dto;

    $this->create_shard_db($shard_dto);
  }

  public function get_db_schema($shard_id, $master = true) {
    if ($master)
      $actor = $this->get_property($shard_id . '_master');
    else
      $actor = $this->get_property($shard_id . '_slave');
    return $actor->get_value()->get_component(MySQL_Schema::get_class_name());
  }

  public function get_db_util($shard_id, $master = true) {
    if ($master)
      $actor = $this->get_property($shard_id . '_master');
    else
      $actor = $this->get_property($shard_id . '_slave');
    return $actor->get_value()->get_component(MySQL_Util::get_class_name());
  }

  public function get_db_connecter($shard_id, $master = true) {
    if ($master)
      $actor = $this->get_property($shard_id . '_master');
    else
      $actor = $this->get_property($shard_id . '_slave');
    return $actor->get_value()->get_component(MySQL_Connecter::get_class_name());
  }

  private function create_shard_db($shard_dto) {
    $path = $this->get_parent_path() . '/dbs/' . $shard_dto->id . '/master';
    $this->set_property($shard_dto->id . '_master', $this->create_db_actor($path, $shard_dto->master));

    $path = $this->get_parent_path() . '/dbs/' . $shard_dto->id . '/slave';
    $this->set_property($shard_dto->id . '_slave', $this->create_db_actor($path, $shard_dto->slave));
  }

  private function create_db_actor($path, $db_dto) {
    $actor = Kernel::get_instance()->new_object(Actor::get_class_name(), $path); 
    $connecter = $actor->add_component(MySQL_Connecter::get_class_name());
    $actor->add_component(MySQL_Schema::get_class_name());
    $actor->add_component(MySQL_Util::get_class_name());
    $connecter->connect($db_dto->host, $db_dto->user, $db_dto->password, $db_dto->port, $db_dto->persistent);
    return $actor;
  }

  private $tables;
  private $shards;
}
