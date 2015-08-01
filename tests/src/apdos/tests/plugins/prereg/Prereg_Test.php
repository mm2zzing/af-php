<?php
namespace apdos\tests\plugins\prereg;

use apdos\kernel\core\Kernel;
use apdos\plugins\test\Test_Case;
use apdos\plugins\test\Test_Suite;
use apdos\plugins\prereg\models\prereg;
use apdos\plugins\prereg\accessors\prereg_user;
use apdos\plugins\prereg\dto\prereg_user_dto;
use apdos\tests\plugins\prereg\mock_prereg_storage;
use apdos\tests\plugins\prereg\afreeca_prereg_user_dto;

class Prereg_Test extends Test_Case {
  const REGISTER_EMAIL = 'test@mail.com';
  const REGISTER_PHONENUMBER = '010-0000-0000';
  const REGISTER_AFREECA_ID = 'testid';

  private $prereg = null;
  private $storage = null;

  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  /**
   * 사전 등록 유저 정보 등록
   */
  public function test_register() {
    $this->prereg->start($this->storage, 'apdos\plugins\prereg\dto\Prereg_User_DTO');

    $pre_user = $this->prereg->register_by_email(self::REGISTER_EMAIL, array());
    $user_dto = $pre_user->get_prereg_user_dto();
    $this->assert(0 == strcmp($user_dto->email, self::REGISTER_EMAIL), 'register email is exist');
  }

  public function test_register_with_values() {
    $this->prereg->start($this->storage, 'apdos\tests\plugins\prereg\Afreeca_Prereg_User_DTO');

    $values = array('afreeca_id'=>self::REGISTER_AFREECA_ID);
    $pre_user = $this->prereg->register_by_email(self::REGISTER_EMAIL, $values);
    $user_dto = $pre_user->get_prereg_user_dto();
    $this->assert(0 == strcmp($user_dto->email, self::REGISTER_EMAIL), 'register email is exist');
    $this->assert(0 == strcmp($user_dto->afreeca_id, self::REGISTER_AFREECA_ID), 'afreeca_id is exist');
  }

  public function set_up() {
    $actor = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/sys/prereg');
    $this->prereg = $actor->add_component('apdos\plugins\prereg\models\Prereg');
    $this->storage = $actor->add_component('apdos\tests\plugins\prereg\Mock_Prereg_Storage');
  }

  public function tear_down() {
    $this->prereg = null;
    Kernel::get_instance()->delete_object('/sys/prereg');
  }

  public static function create_suite() {
    $suite = new Test_Suite('Prereg_Test');
    $suite->add(new Prereg_Test('test_register'));
    $suite->add(new Prereg_Test('test_register_with_values'));
    return $suite;
  }
}


