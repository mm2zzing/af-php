<?php
namespace apdos\plugins\db_connecters\mongodb;

/** 
 * @class Replicat_Set_Acknowleged
 *
 * @breif 리플리카 셋의 메모리에 쓰기 작업이 적용되었느지 확인할 수 있다.
 */
class Replicat_Set_Acknowleged_Write implements Write_Concern {
  private $server_count;

  /**
   * Constructor
   *
   * @param primary_and_seconary_server_count int 프라이머리 서버를 포함한 리플리카셋의 총 서버 갯수
   */
  public function __construct($primary_and_seconary_server_count) {
    $this->server_count = $primary_and_seconary_server_count;
  }

  public function get_options() {
    return array('w'=>$this->server_count);
  }
}
