<?php
namespace apdos\plugins\database\connecters\mongodb;

class Unkacknowleged_Write implements Write_Concern {
  public function get_options() {
    return array('w'=>0);
  }
}
