<?php
namespace af\plugins\router\dto;

class Router_DTO {
  private $register_gets = array();

  /**
   *
   * @param register_gets Array 라우팅 연결 정보
   */
  public function __construct($register_gets) {
    foreach ($register_gets as $register_get) {
      $dto = new Register_Get_DTO($register_get);
      array_push($this->register_gets, $dto);
    }
  } 

  public function get_register_gets() {
    return $this->register_gets;
  }

  public function is_null() {
    return false;
  }

}
