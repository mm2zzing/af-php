<?php
namespace tests\apdos\kernel\actor\property;

use apdos\plugins\test\Test_Suite;
use apdos\plugins\test\Test_Case;
use apdos\kernel\core\Kernel;

class Property_Test extends Test_Case {
  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function test_get() {
    $actor = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/test1');
    $component = $actor->add_component('tests\apdos\kernel\actor\Test_Component');
  }

  public static function create_suite() {
    $suite = new Test_Suite('Property_Test');
    $suite->add(new Property_Test('test_get'));
    return $suite;
  }
}


