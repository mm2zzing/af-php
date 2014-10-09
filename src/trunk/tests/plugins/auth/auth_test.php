<?php
require_once 'apdt/plugins/test/test_case.php';
require_once 'apdt/plugins/auth/auth.php';
require_once 'apdt/plugins/auth/accessors/user.php';
require_once 'apdt/plugins/auth/dto/user_dto.php';
require_once 'apdt/tests/plugins/auth/mock_auth_storage.php';

class Auth_Test extends Test_Case {
  const REGISTER_ID = 'testid';
  const REGISTER_PASSWORD = 'testpassword';
  const REGISTER_EMAIL = 'test@mail.com';
  const UUID = "21312312-123123-1231-123123123123";
  const DEVICE_ID = "0302342342-234234-234234-234234234234";

  private $auth;

  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  /**
   * 게스트 회원 가입 
   */
  public function test_register_guest() {
    $user = $this->auth->register_guest();
    $user_dto = $user->get_user_dto();
    $this->assert(strlen($user_dto->register_id) == 0, 'register id length is 0');
    $this->assert(strlen($user_dto->register_password) == 0, 'register password length is 0');
    $this->assert(strlen($user_dto->register_email) == 0, 'register email length is 0');
    $this->assert(strlen($user_dto->device_id) == 0, 'device id length is 0');
    $this->assert(strlen($user_dto->uuid) > 0, 'uuid length is great than 0');
    $this->assert(strlen($user_dto->install_ip) > 0, 'install ip is great than 0');
    $this->assert(strlen($user_dto->install_date) > 0, 'install date is great than 0');
  }

  /**
   * 일반 회원 가입
   */
  public function test_register() {
    $user = $this->auth->register(self::REGISTER_ID, self::REGISTER_PASSWORD, self::REGISTER_EMAIL);
    $user_dto = $user->get_user_dto();
    $this->assert(strlen($user_dto->register_id) > 0, 'register id length is great than 0');
    $this->assert(strlen($user_dto->register_password) > 0, 'register password length is great than 0');
    $this->assert(strlen($user_dto->register_email) > 0, 'register email length is great than 0');
    $this->assert(strlen($user_dto->uuid) > 0, 'uuid length is great than 0');
    $this->assert(strlen($user_dto->install_ip) > 0, 'install ip is great than 0');
    $this->assert(strlen($user_dto->install_date) > 0, 'install date is great than 0');

  }

  /**
   * 디바이스 기반 회원 가입
   */
  public function test_register_device() {
    $user = $this->auth->register_device(self::DEVICE_ID);
    $user_dto = $user->get_user_dto();
    $this->assert(strlen($user_dto->register_id) == 0, 'register id length is 0');
    $this->assert(strlen($user_dto->register_password) == 0, 'register password length is 0');
    $this->assert(strlen($user_dto->register_email) == 0, 'register email length is 0');
    $this->assert(strlen($user_dto->uuid) > 0, 'uuid length is great than 0');
    $this->assert(strlen($user_dto->device_id) > 0, 'device length is great than 0');
    $this->assert(strlen($user_dto->install_ip) > 0, 'install ip is great than 0');
    $this->assert(strlen($user_dto->install_date) > 0, 'install date is great than 0');

  }

  public function test_get_user() {
    $user = $this->auth->get_user(array('uuid'=>self::UUID));
    $this->assert(true == $user->is_null(), 'user is null');
    $user = $this->auth->register_guest();
    $this->assert(false == $user->is_null(), 'user is not null');
  } 

  public function test_login() {
    $occur_excption = false;
    try {
      $this->auth->login(self::REGISTER_ID, self::REGISTER_PASSWORD);
    }
    catch (Auth_Id_Is_None $e) {
      $occur_excption = true;
    }
    $this->assert(true == $occur_excption, 'auth id is none exception');

    $user = $this->auth->register(self::REGISTER_ID, self::REGISTER_PASSWORD, self::REGISTER_EMAIL);
    $this->assert(false == $user->is_null(), 'user is not null');

    $user = $this->auth->login(self::REGISTER_ID, self::REGISTER_PASSWORD);
    $this->assert(false == $user->is_null(), 'user is not null');
  }

  /**
   * 회원 탈퇴 테스트.
   *
   */
  public function test_unregister() {
    $occur_exception = false;
    try {
      $this->auth->unregister(self::UUID);
    }
    catch (Auth_Uuid_Is_None $e) {
      $occur_excption = true;
    }
    $this->assert(true == $occur_excption, 'uuid is none');

    $user = $this->auth->register_guest();
    $user_dto = $user->get_user_dto();

    $this->assert(false == $user->is_null(), 'user is not null');
    $this->assert(false == $user_dto->unregistered, 'user unregistered is false');

    $this->auth->unregister($user_dto->uuid);

    $user = $this->auth->get_user(array('uuid'=>$user_dto->uuid));
    $user_dto = $user->get_user_dto();
    $this->assert(false == $user->is_null(), 'user is not null');
    $this->assert(true == $user_dto->unregistered, 'user unregistered is false');
  }

  /**
   * 회월 탈퇴후 로그인 
   */ 
  public function test_unregister_login() {
    $user = $this->auth->register(self::REGISTER_ID, self::REGISTER_PASSWORD, self::REGISTER_EMAIL);
    $user_dto = $user->get_user_dto();
    $this->auth->unregister($user_dto->uuid);
    $occur_exception = false;
    try {
      $this->auth->login($user_dto->register_id, $user_dto->register_password);
    }
    catch (Auth_Is_Unregistered $e) {
      $occur_excption = true;
    }
    $this->assert(true == $occur_excption, 'occour exception');
  }

  public function set_up() {
    $actor = Kernel::get_instance()->new_object('Actor', '/sys/auth');
    $this->auth = $actor->add_component('Auth');
    $storage = $actor->add_component('Mock_Auth_Storage');
    $this->auth->start($storage, 'User_DTO');
  }

  public function tear_down() {
    $this->auth = null;
  }
}

