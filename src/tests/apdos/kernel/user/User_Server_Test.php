<?php
namespace tests\apdos\kernel\user;

use apdos\plugins\test\Test_Case;
use apdos\plugins\test\Test_Suite;
use apdos\kernel\user\User_Server;
use apdos\kernel\user\errors\Impossible_Login_User_Error;
use apdos\kernel\etc\Etc;
use apdos\kernel\user\handlers\Etc_Handler;

class User_Server_Test extends Test_Case {
  private $server;

  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function test_create() {
    $user = $this->server->get_login_user();
    $this->assert($user->get_name() == User_Server::ROOT_USER, "default login user is root");
  } 

  public function test_change_user() {
    $this->server->change_user(User_Server::ROOT_USER);
    $user = $this->server->get_login_user();
    $this->assert($user->get_name() == User_Server::ROOT_USER, "user is root");
    $this->assert($user->get_password() == '', 'password is \'\'');
    $this->assert($user->is_possible_login() == true, 'root is login possible'); 
  }

  public function test_change_app_user() {
    $this->server->change_user('launcher');
    $user = $this->server->get_login_user();
    $this->assert($user->get_name() == 'launcher', "user is launcher");
    $this->assert($user->get_password() == '*', 'password is \'*\'');
    $this->assert($user->is_possible_login() == false, 'launcher is login impossible');
  }

  public function test_user_login() {
    $user = $this->server->login(User_Server::ROOT_USER, '');
    $this->assert($user->get_name() == User_Server::ROOT_USER, "user is root");
    $this->assert($user->get_password() == '', 'password is \'\'');
    $this->assert($user->is_possible_login() == true, 'root is login possible'); 

    $user = $this->server->get_login_user();
    $this->assert($user->get_name() == User_Server::ROOT_USER, "user is root");
  }

  public function test_app_user_login() {
    $occur_exception = false;
    try {
      $this->server->login('launcher', '*');
    }
    catch (Impossible_Login_User_Error $e) {
      $occur_exception = true;
    }
    $this->assert($occur_exception == true, 'application user is impossible login');
  }

  public function test_logout() {
    $this->server->logout();
    $user = $this->server->get_login_user();
    $this->assert($user->is_null() == true, 'login user is null');
  }

  public function test_register() {
    $user = $this->server->register('test', '1234');
    $this->assert($user->get_name() == 'test', 'register user name is test');
  }

  public function set_up() {
    $this->server = User_Server::get_instance();
    $this->server->load(new Test_Data_Handler());
  }

  public function tear_down() {
  }

  public static function create_suite() {
    $suite = new Test_Suite('User_Server_Test');
    $suite->add(new User_Server_Test('test_create'));
    $suite->add(new User_Server_Test('test_change_user'));
    $suite->add(new User_Server_Test('test_change_app_user'));
    $suite->add(new User_Server_Test('test_user_login'));
    $suite->add(new User_Server_Test('test_app_user_login'));
    $suite->add(new User_Server_Test('test_logout'));
    $suite->add(new User_Server_Test('test_register'));
    return $suite;
  }
}

