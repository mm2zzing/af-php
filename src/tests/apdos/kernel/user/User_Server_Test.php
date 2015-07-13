<?php
namespace tests\apdos\kernel\user;

use apdos\plugins\test\Test_Case;
use apdos\plugins\test\Test_Suite;
use apdos\kernel\user\User_Server;
use apdos\kernel\user\errors\Impossible_Login_User_Error;
use apdos\kernel\user\errors\Not_Exist_User_Error;
use apdos\kernel\etc\Etc;
use apdos\kernel\user\handlers\Etc_Handler;
use apdos\kernel\actor\Actor;
use apdos\kernel\core\Kernel;

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
  }

  public function test_change_app_user() {
    $this->server->change_user('launcher');
    $user = $this->server->get_login_user();
    $this->assert($user->get_name() == 'launcher', "user is launcher");
    $this->assert($user->get_password() == '', 'password is \'\'');
  }

  public function test_root_login() {
    $user = $this->server->login(User_Server::ROOT_USER, '');
    $this->assert($user->get_name() == User_Server::ROOT_USER, "user is root");
    $this->assert($user->get_password() == '', 'password is \'\'');

    $user = $this->server->get_login_user();
    $this->assert($user->get_name() == User_Server::ROOT_USER, "user is root");
  }

  public function test_user_login() {
    $user = $this->server->login('launcher', '');
    $this->assert($user->get_name() == 'launcher', 'user is launcher');

    $occur_failed = false;
    try {
      $user = $this->server->login('foo', '');
    }
    catch (Not_Exist_User_Error $e) {
      $occur_failed = true;
    }
    $this->assert($occur_failed == true, 'not exist user login is fail');
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

  public function test_permission() {
    $user = $this->server->login(User_Server::ROOT_USER, '');

    $actor = Kernel::get_instance()->new_object(Actor::get_class_name(), '/test/actor');
    $owner = $actor->get_owner();
    $this->assert($owner->get_name() == User_Server::ROOT_USER, 'Owner is root');

    $this->assert($actor->get_permission()->to_string() == 'rwxrwxr--', 'Owner permission is rwxrwxr--');
  }

  public function set_up() {
    $this->server = User_Server::get_instance();
    // @TODO config 데이터 로드하기
    $this->server->load(new Test_Data_Handler());
  }

  public function tear_down() {
    $this->server->login('launcher', '');
  }

  public static function create_suite() {
    $suite = new Test_Suite('User_Server_Test');
    $suite->add(new User_Server_Test('test_create'));
    $suite->add(new User_Server_Test('test_change_user'));
    $suite->add(new User_Server_Test('test_change_app_user'));
    $suite->add(new User_Server_Test('test_root_login'));
    $suite->add(new User_Server_Test('test_user_login'));
    $suite->add(new User_Server_Test('test_logout'));
    $suite->add(new User_Server_Test('test_register'));
    $suite->add(new User_Server_Test('test_permission'));
    return $suite;
  }
}

