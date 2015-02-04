<?php
namespace apdos\kernel\user;

class User {
  private $name;
  private $password;

  /**
   * Constructor
   *
   * @param name string 유저명
   * @param password string 암호화된 유저 패스워드
   */
  public function __construct($name, $password) {
    $this->name = $name;
    $this->password = $password;
  }

  public function get_name() {
    return $this->name;
  }

  public function get_password() {
    return $this->password;
  }

  /**
   * 로그인 가능한 계정인지 조회
   *
   * @ret bool 로그인 가능 여부
   */
  public function is_login_enable() {
    return $this->password != '*' ? true : false;
  }

  public function is_null() {
    return false;
  }
}
