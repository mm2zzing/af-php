<?php
namespace af\plugins\router\dto;

class Register_Get_DTO {
  private $uri;
  private $controller_class;

  /**
   *
   * @param register_get stdClass 라우팅 정보
   */
  public function __construct($register_get) {
    $this->uri = $register_get->uri;
    $this->controller_class = $register_get->controller_class;
  }

  public function get_uri() {
    return $this->uri;
  }

  public function get_controller_class() {
    return $this->controller_class;
  }

  public function is_null() {
    return false;
  }
}
