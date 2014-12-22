<?php
namespace apdos\plugins\database\connectors\mongodb;

/** 
 * @class Acknowleged
 *
 * @breif 프라이머리 서버의 메모리에 쓰기 작업이 적용되었느지 확인할 수 있다.
 */
class Acknowleged_Write implements Write_Concern {
  public function get_options() {
    return array('w'=>1);
  }
}
