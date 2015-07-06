<?php
namespace apdos\plugins\database\connecters\mysql;

use apdos\kernel\actor\Component;
use apdos\plugins\database\base\rdb\RDB_Util;
use apdos\plugins\database\connecters\mysql\MySQL_Connecter;
use apdos\plugins\database\connecters\mysql\MySQL_Schema;

class MySQL_Util extends RDB_Util { 
  public function has_database($name) {
    return $this->get_schema(MySQL_Schema::get_class_name())->has_database($name);
  }
}
