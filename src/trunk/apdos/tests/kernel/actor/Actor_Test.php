<?php
namespace apdos\tests\kernel\actor;

use apdos\plugins\test\Test_Case;
use apdos\kernel\core\Kernel;
use apdos\tests\kernel\actor\Test_Component;

class Actor_Test extends Test_Case {
  private $kernel;
  private $actor;

  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function test_create() {
    $this->assert(0 == strcmp('actor1', $this->actor->get_name()), 'actor name is actor1');
    $this->assert(0 == strcmp('/temp/actor1', $this->actor->get_path()), 'actor path is /temp/actor1');
  }

  /**
   * @TODO Kernel에서 트리구조로 객체 생성하는 기능 구현후 테스트 케이스 추가 
   *
   */
  public function test_add_child() {
    /*
    $child = $this->kernel->new_object('apdos\kernel\actor\Actor', '/test1');
    $this->assert($child->get_name() == 'test1', 'child actor name is test1');
    $this->assert($child->get_path() == '/test1', 'child actor path is /test1');

    $this->actor->add_child($child);
    $this->assert($child->get_name() == 'test1', 'child actor name is test1');
    $this->assert($child->get_path() == '/temp/actor1/test1', 'child actor path is /temp/actor1/test1');
    */
  }

  public function test_add_component() {
    $find_component = $this->actor->get_component('apdos\kernel\actor\Component');
    $this->assert(true == $find_component->is_null(), 'find component is null');
    $component = $this->actor->add_component('apdos\kernel\actor\Component');
    $find_component = $this->actor->get_component('apdos\kernel\actor\Component');
    $this->assert(false == $find_component->is_null(), 'find component is not null');
  }

  public function test_add_components() {
    $find_component = $this->actor->add_component('apdos\tests\kernel\actor\Test_Component');
    $find_component = $this->actor->add_component('apdos\tests\kernel\actor\Test_Component');
    $find_component = $this->actor->add_component('apdos\kernel\actor\Component');
    $components = $this->actor->get_components('apdos\tests\kernel\actor\Test_Component');
    $this->assert(2 == count($components), 'Test_Component count is 2');
    $components = $this->actor->get_components('apdos\kernel\actor\Component');
    $this->assert(1 == count($components), 'Component count is 1');
  }

  public function test_remove_component() {
    $this->actor->add_component('apdos\kernel\actor\Component');
    $this->actor->remove_component('apdos\kernel\actor\Component');
    $component = $this->actor->get_component('apdos\kernel\actor\Component');
    $this->assert(true == $component->is_null(), 'component is null');
  }

  public function set_up() {
    $this->kernel = new Kernel();
    $this->actor = $this->kernel->new_object('apdos\kernel\actor\Actor', '/temp/actor1');
  }

  public function tear_down() {
    $this->kernel = null;
    $this->actor = null;
  }
}
