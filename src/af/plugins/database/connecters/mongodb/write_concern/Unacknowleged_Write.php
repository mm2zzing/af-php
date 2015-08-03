<?php
namespace af\plugins\database\connecters\mongodb\write_concern;

class Unkacknowleged_Write implements Write_Concern {
  public function get_options() {
    return array('w'=>0);
  }
}
