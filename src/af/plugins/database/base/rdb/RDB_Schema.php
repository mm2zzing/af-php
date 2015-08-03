<?php
namespace af\plugins\database\base\rdb;

use af\kernel\actor\Component;
use af\plugins\database\base\rdb\errors\RDB_Error;

abstract class RDB_Schema extends Component {
  abstract public function create_database($name, $if_not_exists = true);
  abstract public function drop_database($name, $if_exists = true);
  abstract public function has_database($name);
  abstract public function create_table($name, $fields);
  abstract public function drop_table($name, $if_exists = true);

  protected function get_connecter($class_name) {
    $result = $this->get_component($class_name);
    if ($result->is_null())
      throw new RDB_Error('Connecter is null', RDB_Error::CONNECTER_IS_NULL);
    return $result;
  }
}

