<?php
namespace af\plugins\database\base\rdb;

use af\kernel\actor\Component;
use af\plugins\database\base\rdb\errors\RDB_Error;

abstract class RDB_Util extends Component {
  abstract public function has_database($name);

  protected function get_schema($class_name) {
    $result = $this->get_component($class_name);
    if ($result->is_null())
      throw new RDB_Error('Shema is null', RDB_Error::SCHEMA_IS_NULL);
    return $result;
  }

  protected function get_connecter($class_name) {
    $result = $this->get_component($class_name);
    if ($result->is_null())
      throw new RDB_Error('Connecter is null', RDB_Error::CONNECTER_IS_NULL);
    return $result;
  }
}

