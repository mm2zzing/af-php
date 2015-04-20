<?php
namespace apdos\kernel\user\handlers;

use apdos\kernel\etc\Etc;

class Etc_Handler extends User_Data_Handler {
  private $user_names_path;

  public function __construct($user_names_path) {
    $this->user_names_path = $user_names_path;
  }

  public function get_users() {
    return Etc::get_instance()->get($this->user_names_path);
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
    Etc::get_instance()->push($this->user_names_path,  $user);
  }
}
