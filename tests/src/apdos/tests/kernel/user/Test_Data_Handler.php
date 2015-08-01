<?php
namespace apdos\tests\kernel\user;

use apdos\kernel\user\handlers\User_Data_Handler;

class Test_Data_Handler extends User_Data_Handler{

  public function __construct() {
    $this->users = $this->create_dummy_users();
  }
  
  public function get_users() {
    return $this->users;
  }

  /**
   *
   * @param user
   */
  public function add_user($name, $password, $create_date) {
    $user = new \stdClass;
    $user->name = $name;
    $user->password = $password;
    $user->create_date = $create_date;
    array_push($this->users, $user);
  }

  private function create_dummy_users() {
    $result = array();

    $user = new \stdClass();
    $user->name = 'root';
    $user->password = '';
    $user->create_date = '';
    array_push($result, $user);

    $user = new \stdClass();
    $user->name = 'launcher';
    $user->password = '';
    $user->create_date = '';
    array_push($result, $user);

    return $result;
  }
}
