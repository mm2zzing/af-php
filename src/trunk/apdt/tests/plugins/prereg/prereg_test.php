<?php
require_once 'apdt/plugins/test/test_case.php';
require_once 'apdt/plugins/prereg/models/prereg.php';
require_once 'apdt/plugins/prereg/accessors/prereg_user.php';
require_once 'apdt/plugins/prereg/dto/prereg_user_dto.php';
require_once 'apdt/tests/plugins/prereg/mock_prereg_storage.php';
require_once 'apdt/tests/plugins/prereg/afreeca_prereg_user_dto.php';

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
    $this->prereg->start($this->storage, 'Prereg_User_DTO');

    $pre_user = $this->prereg->register_by_email(self::REGISTER_EMAIL, array());
    $user_dto = $pre_user->get_pre_user_dto();
    $this->assert(0 == strcmp($user_dto->email, self::REGISTER_EMAIL), 'register email is exist');
  }

  public function test_register_with_values() {
    $this->prereg->start($this->storage, 'Afreeca_Prereg_User_DTO');

    $values = array('afreeca_id'=>self::REGISTER_AFREECA_ID);
    $pre_user = $this->prereg->register_by_email(self::REGISTER_EMAIL, $values);
    $user_dto = $pre_user->get_pre_user_dto();
    $this->assert(0 == strcmp($user_dto->email, self::REGISTER_EMAIL), 'register email is exist');
    $this->assert(0 == strcmp($user_dto->afreeca_id, self::REGISTER_AFREECA_ID), 'afreeca_id is exist');
  }

  public function set_up() {
    $actor = Kernel::get_instance()->new_object('Actor', '/sys/prereg');
    $this->prereg = $actor->add_component('Prereg');
    $this->storage = $actor->add_component('Mock_Prereg_Storage');
  }

  public function tear_down() {
    $this->prereg = null;
    Kernel::get_instance()->delete_object('/sys/prereg');
  }
}


