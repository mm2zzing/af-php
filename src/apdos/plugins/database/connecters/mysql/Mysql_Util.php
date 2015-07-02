<?php
namespace apdos\plugins\database\connecters\mysql;

use apdos\kernel\actor\Component;
use apdos\plugins\database\connecters\mysql\Mysql_Connecter;
use apdos\plugins\database\connecters\mysql\Mysql_Schema;

class Mysql_Util extends Component { 
  public function has_database($name) {
    return $this->get_schema()->has_database($name);
  }

  private function get_schema() {
    $result = $this->get_component(Mysql_Schema::get_class_name());
    if ($result->is_null())
      throw new Mysql_Error('Shema is null', Mysql_Error::SCHEMA_IS_NULL);
    return $result;
  }

  private function get_connecter() {
    $result = $this->get_component(Mysql_Connecter::get_class_name());
    if ($result->is_null())
      throw new Mysql_Error('Connecter is null', Mysql_Error::CONNECTER_IS_NULL);
    return $result;
  }
}
