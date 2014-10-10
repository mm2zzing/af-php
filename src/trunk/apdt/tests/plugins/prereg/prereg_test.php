<?php
require_once 'apdt/plugins/test/test_case.php';
require_once 'apdt/plugins/prereg/prereg.php';
require_once 'apdt/plugins/prereg/accessors/prereg_user.php';
require_once 'apdt/plugins/prereg/dto/prereg_user_dto.php';
require_once 'apdt/tests/plugins/prereg/mock_prereg_storage.php';

class Prereg_Test extends Test_Case {
  const REGISTER_EMAIL = 'test@mail.com';
  const REGISTER_PHONENUMBER = '010-0000-0000';

  private $prereg = null;

  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  /**
   * 사전 등록 유저 정보 등록
   */
  public function test_register() {
    $pre_user = $this->prereg->register_by_email(self::REGISTER_EMAIL, array());
    $user_dto = $pre_user->get_pre_user_dto();
  }

  public function set_up() {
    $actor = Kernel::get_instance()->new_object('Actor', '/sys/prereg');
    $this->prereg = $actor->add_component('Prereg');
    $storage = $actor->add_component('Mock_Prereg_Storage');
    $this->prereg->start($storage, 'Prereg_User_DTO');
  }

  public function tear_down() {
    $this->prereg = null;
  }
}


