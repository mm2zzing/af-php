<?php
namespace af\tests\plugins\prereg;

use af\kernel\core\Kernel;
use af\plugins\test\Test_Case;
use af\plugins\test\Test_Suite;
use af\plugins\prereg\models\prereg_manager;
use af\plugins\prereg\accessors\prereg_user;
use af\plugins\prereg\dto\prereg_user_dto;
use af\tests\plugins\prereg\mock_prereg_manager_storage;
use af\tests\plugins\prereg\afreeca_prereg_user_dto;

class Prereg_Manager_Test extends Test_Case {
  const REGISTER_EMAIL = 'test@mail.com';
  const REGISTER_PHONENUMBER = '010-0000-0000';
  const REGISTER_AFREECA_ID = 'testid';

  private $prereg_manager = null;
  private $storage = null;

  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  /**
   *
   *
   */
  public function test_setup() {
  }

  /**
   * 사전 등록 유저 리스트 조회 
   */
  public function test_get_prereg_users() {

    $pre_users = $this->prereg_manager->get_prereg_users();
    $this->assert(count($pre_users) > 0, "prereg users count is great than 0");
    foreach ($pre_users as $pre_user) {
      $user_dto = $pre_user->get_prereg_user_dto();
      $this->assert(0 == strcmp($user_dto->email, self::REGISTER_EMAIL), 'register email is exist');
    }
  }

  public function set_up() {
    $actor = Kernel::get_instance()->new_object('af\kernel\actor\Actor', '/sys/prereg_manager');
    $this->prereg_manager = $actor->add_component('af\plugins\prereg\models\Prereg_Manager');
    $this->storage = $actor->add_component('af\tests\plugins\prereg\Mock_Prereg_Manager_Storage');
    $this->prereg_manager->start($this->storage, 'af\plugins\prereg\dto\Prereg_User_DTO'); 
  }

  public function tear_down() {
    $this->prereg_manager = null;
    Kernel::get_instance()->delete_object('/sys/prereg_manager');
  }

  public static function create_suite() {
    $suite = new Test_Suite('Prereg_Manager_Test');
    $suite->add(new Prereg_Manager_Test('test_get_prereg_users'));
    return $suite;
  }
}


