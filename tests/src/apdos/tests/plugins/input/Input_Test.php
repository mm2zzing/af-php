<?php
namespace apdos\tests\plugins\input;

use apdos\plugins\test\Test_Case;
use apdos\plugins\test\Test_Suite;
use apdos\plugins\input\Input;


class Input_Test extends Test_Case {
  const STUB_REMOTE_ADDR = '192.168.1.2';
  const STUB_HTTP_USER_AGENT = 'Mozilla/5.0 (Windows; U; NT 5.1; en-US; rv:1.7) Gecko/20040803 Firefox/0.9.3';

  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function test_get() {
    $pet = Input::get_instance()->get('pet');
    $this->assert($pet == '', 'pet is empty');

    $this->prepare_get('pet', 'dog');
    $pet = Input::get_instance()->get('pet');
    $this->assert($pet == 'dog', 'pet is dog');

    $foo = Input::get_instance()->get('foo', 'bar');
    $this->assert($foo == 'bar', 'Default value is bar');
  }

  public function test_has() {
    $this->assert(Input::get_instance()->has('pet') == false, 'Not exist pet');
    $this->prepare_get('pet', 'dog');
    $this->assert(Input::get_instance()->has('pet') == true, 'Exist pet');
  }

  public function test_get_ip() {
    $this->prepare_server('REMOTE_ADDR', self::STUB_REMOTE_ADDR);
    $this->assert(self::STUB_REMOTE_ADDR == Input::get_instance()->get_ip(), 'ip is 192.168.1.2');
  }

  public function test_get_user_agent() {
    $this->prepare_server('HTTP_USER_AGENT', self::STUB_HTTP_USER_AGENT);
    $this->assert(Input::get_instance()->get_user_agent() == self::STUB_HTTP_USER_AGENT, 'user agent is firefox');
  }

  public function set_up() {
    $this->clear_get();
    $this->clear_post();
  }

  public function tear_down() {
  }

  private function prepare_get($key, $value) {
    $_GET[$key] = $value;
  }

  private function prepare_post($key, $value) {
    $_POST[$key] = $value;
  }

  private function prepare_server($key, $value) {
    $_SERVER[$key] = $value;
  }

  private function clear_get() {
    foreach ($_GET as $key=>$value){ 
      unset($_GET[$key]);
    }
  }

  private function clear_post() {
    foreach ($_POST as $key=>$value){ 
      unset($_POST[$key]);
    }
  }

  public static function create_suite() {
    $suite = new Test_Suite('Input_Test');
    $suite->add(new Input_Test('test_get'));
    $suite->add(new Input_Test('test_has'));
    $suite->add(new Input_Test('test_get_ip'));
    $suite->add(new Input_Test('test_get_user_agent'));
    return $suite;
  }
}

