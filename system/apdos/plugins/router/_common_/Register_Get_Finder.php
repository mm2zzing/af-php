<?php
namespace apdos\plugins\router\_common_;

use apdos\plugins\router\dto\Null_Register_Get_DTO;

class Register_Get_Finder {
  private $register_gets;
  private $uri_tokens;

  public function __construct($register_gets) {
    $this->register_gets = $register_gets;
  }

  public function find($uri) {
    $this->uri_tokens = explode('/', $uri);
    foreach ($this->register_gets as $register_get) {
      $reg_uri = $register_get->get_uri();
      if ($this->is_root_uri($reg_uri)) {
        if ($this->is_equal($reg_uri, $uri))
          return $register_get;
      }
      else {
        $this->reg_uri_tokens = explode('/', $reg_uri);
        if ($this->is_equal_format($reg_uri, $uri))
          return $register_get;
      }
    }
    return new Null_Register_Get_DTO();
  }

  private function is_root_uri($uri) {
    return $uri == '/' ? true : false;
  }

  private function is_equal($reg_uri, $uri) {
    return $reg_uri == $uri;
  }

  private function is_equal_format($reg_uri, $uri) {
    // uri의 토큰 갯수가 동일한지 조회
    if (count($this->uri_tokens) == count($this->reg_uri_tokens)) {
      $path = $this->extract_cotroller_uri($reg_uri);
      if (strpos($uri, $path) !== false)
        return true;
    }
    return false;
  }


  /**
   * 콘트롤러의 path로 지정할 부분을 가져온다.
   *
   * @param reguster_uri string router.json에 등록되어 있는 URI 포맺
   */
  private function extract_cotroller_uri($register_uri) {
    $result = strstr($register_uri, '/{', true);
    if ($result == '')
      return $register_uri;
    else
      return $result;
  }
}
