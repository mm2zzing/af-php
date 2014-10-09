<?php
require_once 'apdt/plugins/test/test_case.php';
require_once 'apdt/kernel/core/kernel.php';
require_once 'apdt/kernel/actor/actor.php';

class Actor_Test extends Test_Case {
  private $kernel;
  private $actor;

  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function test_create() {
    $this->assert(0 == strcmp('actor1', $this->actor->get_name()), 'actor name is actor1');
  }

  public function test_add_component() {
    $find_component = $this->actor->get_component('Component');
    $this->assert(true == $find_component->is_null(), 'find component is null');
    $component = $this->actor->add_component('Component');
    $find_component = $this->actor->get_component('Component');
    $this->assert(false == $find_component->is_null(), 'find component is not null');
  }

  public function test_remove_component() {
    $this->actor->add_component('Component');
    $this->actor->remove_component('Component');
    $component = $this->actor->get_component('Component');
    $this->assert(true == $component->is_null(), 'component is null');
  }

  public function set_up() {
    $this->kernel = new Kernel();
    $this->actor = $this->kernel->new_object('Actor', '/temp/actor1');
  }

  public function tear_down() {
    $this->kernel = null;
    $this->actor = null;
  }
}
