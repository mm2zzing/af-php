<?php
namespace apdos\plugins\database\connecters\mongodb;

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


