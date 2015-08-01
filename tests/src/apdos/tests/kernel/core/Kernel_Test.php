<?php
namespace apdos\tests\kernel\core;

use apdos\plugins\test\Test_Case;
use apdos\plugins\test\Test_Suite;
use apdos\kernel\core\Kernel;

class Kernel_Test extends Test_Case {
  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function test_create() {
    $kernel = new Kernel();
    $node_class = 'apdos\kernel\core\Root_Node';
    $node_path = '/temp/node1';
    $node = $kernel->new_object($node_class, $node_path);

    $this->assert($node->get_name() == 'node1', 'node name');
  }

  public function test_lookup() {
    Kernel::get_instance()->new_object('apdos\kernel\core\Root_Node', '/usr/node1');
    $this->assert(Kernel::get_instance()->has_object('/usr'), 'Kernel has usr');
    $usr = Kernel::get_instance()->find_object('/usr');
    $this->assert(!$usr->is_null(), 'usr is exist');
    $this->assert($usr->get_path() == '/usr', 'usr path is /usr');
    $this->assert($usr->get_name() == 'usr', 'usr name is usr');
  }

  public static function create_suite() {
    $suite = new Test_Suite('Kernel_Test');
    $suite->add(new Kernel_Test('test_create'));
    $suite->add(new Kernel_Test('test_lookup'));
    return $suite;
  }
}

