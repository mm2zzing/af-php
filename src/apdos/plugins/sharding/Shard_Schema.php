<?php
namespace apdos\plugins\sharding;

use apdos\kernel\core\Kernel;
use apdos\kernel\actor\Component;
use apdos\plugins\sharding\Shard_Router;

class Shard_Schema extends Component {
  private $router;

  public function __construct() {
  }

  public function create_database($shard_id, $name) {
    $mysql_schema = $this->get_router()->get_db_schema($shard_id);
    return $mysql_schema->create_database($name);
  }

  public function drop_database($shard_id, $name) {
    $mysql_schema = $this->get_router()->get_db_schema($shard_id);
    return $mysql_schema->drop_database($name);
  }

  public function create_table($shard_id, $name, $fields) {
    $mysql_schema = $this->get_router()->get_db_schema($shard_id);
    return $mysql_schema->create_table($name, $fields);
  }

  public function drop_table($shard_id) {
    $mysql_schema = $this->get_router()->get_db_schema($shard_id);
    return $mysql_schema->drop_table($name, $fields);
  }

  private function get_router() {
    $result = $this->get_component(Shard_Router::get_class_name());
    if ($result->is_null())
      throw new \Exception("Shard_Router is null");
    return $result;
  }
}
