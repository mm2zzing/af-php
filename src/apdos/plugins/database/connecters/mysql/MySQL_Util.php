<?php
namespace apdos\plugins\database\connecters\mysql;

use apdos\kernel\actor\Component;
use apdos\plugins\database\connecters\mysql\MySQL_Connecter;
use apdos\plugins\database\connecters\mysql\MySQL_Schema;

class MySQL_Util extends Component { 
  public function has_database($name) {
    return $this->get_schema()->has_database($name);
  }

  private function get_schema() {
    $result = $this->get_component(MySQL_Schema::get_class_name());
    if ($result->is_null())
      throw new MySQL_Error('Shema is null', MySQL_Error::SCHEMA_IS_NULL);
    return $result;
  }

  private function get_connecter() {
    $result = $this->get_component(MySQL_Connecter::get_class_name());
    if ($result->is_null())
      throw new MySQL_Error('Connecter is null', MySQL_Error::CONNECTER_IS_NULL);
    return $result;
  }
}
