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

class Shard_Router extends Component { 
  public function __construct() {
  }

  public function select_databases() {
    $shards = $this->get_config()->get_shards();
    foreach ($shards as $shard) {
      $connecter = $this->get_session()->get_db_connecter($shard->get_id(), true);
      $connecter->select_database($shard->get_master()->db_name);
      $connecter = $this->get_session()->get_db_connecter($shard->get_id(), false);
      $connecter->select_database($shard->get_slave()->db_name);
    }
  }

  private function get_config() {
    $component = $this->get_component(Shard_Config::get_class_name());
    if ($component->is_null())
      throw new \Exception('Shard_Config is null');
    return $component;
  }

  private function get_session() {
    $component = $this->get_component(Shard_Session::get_class_name());
    if ($component->is_null())
      throw new \Exception('Shard_Session is null');
    return $component;
  }
}


