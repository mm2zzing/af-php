<?php
interface Write_Concern {
  public function get_options();
}

class Unkacknowleged_Write implements Write_Concern {
  public function get_options() {
    return array('w'=>0);
  }
}

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

/**
 * @class Majority_Acknowleged
 * 
 * @brief "majority"값을 부여하여, 노드의 과반수 이상에 서버의 메모리에 쓰기 작업이 적용되었는지 확인할 수 있다
 */
class Majority_Acknowleged_Write implements Write_Concern {
  public function get_options() {
    return array('w'=>'majority');
  }
}

/**
 * @class Journal
 *
 * @brief 저널링 기능을 사용하고 있다면 사용할 수 있다. 다른 옵션들의 쓰기 모드는 모두
 *        서버의 메모리에 쓰기가 완료되었는지 확인하지만 Joural_Write는 디스크에 데이터가 써진후에야 응답을
 *        해주기 때문에 느리지만 안정적이다. 서버 크래시가 일어나더라도 데이터를 잃어 버릴 염려가 적어진다.
 *        아주 중요한 데이터에만 사용하도록 한다.
 */
class Journal_Write implements Write_Concern {
  public function get_options() {
    return array('j'=>true);
  }
}
