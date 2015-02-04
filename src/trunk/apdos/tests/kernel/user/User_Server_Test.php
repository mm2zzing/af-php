<?php
namespace apdos\tests\kernel\user;

use apdos\plugins\test\Test_Case;
use apdos\kernel\user\User_Server;

class User_Server_Test extends Test_Case {
  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function test_create() {
    $server = User_Server::get_instance();
    $server->load();
    $server->change_user(User_Server::ROOT_USER);

    $user = $server->get_login_user();
    $this->assert($user->get_name() == User_Server::ROOT_USER, "default login user is root");
  }

  public function set_up() {
  }

  public function tear_down() {
  }
}

