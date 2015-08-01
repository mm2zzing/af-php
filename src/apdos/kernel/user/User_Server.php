<?php
namespace apdos\kernel\user;

use apdos\kernel\core\Kernel;
use apdos\kernel\core\Object;
use apdos\kernel\user\errors\Not_Exist_User_Error;
use apdos\kernel\user\errors\Password_Invalid_Error;
use apdos\kernel\user\errors\Impossible_Login_User_Error;
use apdos\kernel\core\Time;
use apdos\kernel\etc\Etc;

/**
 * @class User_Server
 * 
 * @brief
 *
 * @author Lee, Hyeon-gi
 */
class User_Server extends Object {
  const ROOT_USER = "root";

  private $users;
  private $login_user;
  private $data_handler;

  public function __construct() {
    $this->users = array();
    $this->login_user = new User(self::ROOT_USER, '*', '');
  }

  /**
   * 시스템의 유저 정보를 로드한다
   *
   */
  public function load($data_handler) {
    $this->data_handler = $data_handler;
    $this->load_users(); 
  }

  private function load_users() {
    $this->users = array();
    $users = $this->data_handler->get_users();
    foreach ($users as $user) {
      $data = new User($user->name, $user->password, $user->create_date);
      array_push($this->users, $data);
    }
  }

  public function get_login_user() {
    return $this->login_user;
  }

  public function change_user($name) {
    $select_user = new Null_User();
    foreach ($this->users as $user) {
      if ($user->get_name() == $name)
        $select_user = $user;
    }
    if ($select_user->is_null())
      throw new Not_Exist_User_Error("$name is not exist user");
    $this->login_user = $select_user;
  }

  public function login($name, $password) {
    $find_user = $this->find_user($name);
    if ($find_user->is_null())
      throw new Not_Exist_User_Error("$name is not exist user");
    if ($find_user->get_password() != '') {
      //
      // throw new Password_Invalid_Error("$name user password invalid");
    }
    $this->login_user = $find_user;
    return $find_user;
  }

  public function logout() {
    $this->login_user = new Null_User();
  }

  private function find_user($name) {
    foreach ($this->users as $user) {
      if ($user->get_name() == $name)
        return $user;
    }
    return new Null_User();
  }

  public function register($name, $password) {
    // @TODO 추후 커스텀 락 걸어주기
    // 동기화를 위해 가입은 매번 데이터를 로드
    $this->load_users();
    $find_user = $this->find_user($name);
    if (!$find_user->is_null())
      throw new Already_Exist_User_Error("$name is alreay exist");
    $this->data_handler->add_user($name, $password, Time::get_instance()->get_ymd_his());
    // 저장된 데이터를 다시 로드
    $this->load_users();
    return $this->find_user($name);
  }

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $instance = new User_Server();
    }
    return $instance;
  }
}
