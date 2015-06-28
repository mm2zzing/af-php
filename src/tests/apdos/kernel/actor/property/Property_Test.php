<?php
namespace tests\apdos\kernel\actor\property;

use apdos\plugins\test\Test_Suite;
use apdos\plugins\test\Test_Case;
use apdos\kernel\core\Kernel;
use apdos\kernel\actor\events\Component_Event;

class Property_Test extends Test_Case {
  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function test_get() {
    $actor1 = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/test1');
    $test_component1 = $actor1->add_component('tests\apdos\kernel\actor\Test_Component');

    $actor2 = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/test2');
    $test_component2 = $actor2->add_component('tests\apdos\kernel\actor\Test_Component');
    $test_component1->set_property('number', 1000);
    $test_component1->set_property('string', 'hello');
    $test_component1->set_property('test_component2', $test_component2);
    $test_component1->set_property('actor2', $actor2);

    $this->assert_number_and_string_property($test_component1);

    $property = $test_component1->get_property('actor2');
    $this->assert($property->is_null() == false, 'property is exist');
    $this->assert($property->get_name() == 'actor2', 'property name is actor2');
    $property = $test_component1->get_property('test_component2');
    $this->assert($property->is_null() == false, 'property is exist');
    $this->assert($property->get_name() == 'test_component2', 'property name is test_component2');

    $actor2->release(); 

    $this->assert_number_and_string_property($test_component1);

    $property = $test_component1->get_property('actor2');
    $this->assert($property->is_null() == true, 'property is not exist');
    $property = $test_component1->get_property('test_component2');
    $this->assert($property->is_null() == true, 'property is not exist');
  }

  private function assert_number_and_string_property($component) {
    $property = $component->get_property('number');
    $this->assert($property->get_value() == 1000, 'number is 1000');
    $property = $component->get_property('string');
    $this->assert($property->get_value() == 'hello', 'string is hello');
  }

  public function set_up() {
    $this->start_component = false;
  }

  public static function create_suite() {
    $suite = new Test_Suite('Property_Test');
    $suite->add(new Property_Test('test_get'));
    return $suite;
  }

  private $start_component;
}


