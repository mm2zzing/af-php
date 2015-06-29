<?php
namespace apdos\plugins\database\connecters\mysql;

use apdos\kernel\actor\Component;
use apdos\plugins\database\connecters\mysql\Mysql_Connecter;

class Mysql_Util extends Component { 
  private function get_connecter() {
    $result = $this->get_component(Mysql_Connecter::get_class_name());
    if ($result->is_null())
      throw new Mysql_Error('Connecter is null', Mysql_Error::CONNECTER_IS_NULL);
    return $result;
  }
}
