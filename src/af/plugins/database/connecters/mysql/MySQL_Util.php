<?php
namespace af\plugins\database\connecters\mysql;

use af\kernel\actor\Component;
use af\plugins\database\base\rdb\RDB_Util;
use af\plugins\database\connecters\mysql\MySQL_Connecter;
use af\plugins\database\connecters\mysql\MySQL_Schema;

class MySQL_Util extends RDB_Util { 
  public function has_database($name) {
    return $this->get_schema(MySQL_Schema::get_class_name())->has_database($name);
  }
}
