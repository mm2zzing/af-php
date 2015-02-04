<?php
namespace apdos\kernel\user;

use apdos\kernel\actor\Component;
use apdos\kernel\core\Kernel;
use apdos\kernel\user\errors\Not_Exist_User_Error;

class User_Server extends Component {
  const ROOT_USER = "root";

  private $users;
  private $login_user;

  public function __construct() {
    $this->users = array();
    $this->login_user = new Null_User();
  }

  public function load() {
    $fake_user = new User('root', '');
    array_push($this->users, $fake_user);
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

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $actor = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/sys/user_server');
      $instance = $actor->add_component('apdos\kernel\user\User_Server');
    }
    return $instance;
  }
}
