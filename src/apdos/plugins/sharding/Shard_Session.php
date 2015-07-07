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

class Shard_Session extends Component { 
  public function __construct() {
  }

  public function get_db_connecter($shard_id, $master = true) {
    $this->build_shard_db($shard_id);
    $actor = $this->get_property($this->get_db_session_name($shard_id, $master));
    return $actor->get_value()->get_connecter();
  }

  public function get_db_schema($shard_id, $master = true) {
    $this->build_shard_db($shard_id);
    $actor = $this->get_property($this->get_db_session_name($shard_id, $master));
    return $actor->get_value()->get_schema();
  }

  public function get_db_util($shard_id, $master = true) {
    $this->build_shard_db($shard_id);
    $actor = $this->get_property($this->get_db_session_name($shard_id, $master));
    return $actor->get_value()->get_db_util();
  }

  private function build_shard_db($shard_id) {
    $shard = $this->get_config()->get_shard($shard_id);
    if ($shard->is_null())
      throw new \Exception('shard dto is null. shard is ' . $shard_id->get_value());

    $master_name = $this->get_db_session_name($shard_id, true); 
    if ($this->get_property($master_name)->is_null()) {
      $this->set_property($master_name, $this->create_db_session($master_name, $shard->get_master()));
    }

    $slave_name = $this->get_db_session_name($shard_id, false); 
    if ($this->get_property($slave_name)->is_null()) { 
      $this->set_property($slave_name, $this->create_db_session($slave_name, $shard->get_slave()));
    }
  }

  private function get_db_session_name($shard_id, $master) {
    if ($master)
      return $shard_id->to_string() . '_master';
    else
      return $shard_id->to_string() . '_slave';
  }

  private function create_db_session($name, $db_dto) {
    $path = $this->get_parent_path() . '/db_sessions/' . $name;
    $actor = Kernel::get_instance()->new_object(Actor::get_class_name(), $path); 
    $session = $actor->add_component(MySQL_Session::get_class_name());
    $session->add_event_listener(Component_Event::$START, function($event) use(&$db_dto, &$session) {
      $session->get_connecter()->connect($db_dto->host, 
                                         $db_dto->user, 
                                         $db_dto->password, 
                                         $db_dto->port, 
                                         $db_dto->persistent);
      if ($session->get_schema()->has_database($db_dto->db_name))
        $session->get_connecter()->select_database($db_dto->db_name);
    });
    // 동기적으로 실행하고 싶은 경우 적절한 시점에 이벤츠 업데이트 함수를 호출하여 이벤트를 발생시킨다.
    $actor->update_events();
    return $session;
  }

  private function get_config() {
    $component = $this->get_component(Shard_Config::get_class_name());
    if ($component->is_null())
      throw new \Exception('Shard_Config is null');
    return $component;
  }
}
