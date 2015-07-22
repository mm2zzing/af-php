<?php
namespace tests\apdos\kernel\actor\property;

use apdos\plugins\test\Test_Suite;
use apdos\plugins\test\Test_Case;
use apdos\kernel\core\Kernel;
use apdos\kernel\actor\events\Component_Event;
use tests\apdos\kernel\actor\Test_Component;

class Property_Test extends Test_Case {
  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function test_get() {
    $actor1 = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/test1');
    $component1 = $actor1->add_component(Test_Component::get_class_name());

    $actor2 = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/test2');
    $component2 = $actor2->add_component(Test_Component::get_class_name());
    $component1->set_property('number', 1000);
    $component1->set_property('string', 'hello');
    $component1->set_property('component2', $component2);
    $component1->set_property('actor2', $actor2);

    $this->assert_number_and_string_property($component1);

    $property = $component1->get_property('actor2');
    $this->assert($property->is_null() == false, 'property is exist');
    $this->assert($property->get_name() == 'actor2', 'property name is actor2');
    $property = $component1->get_property('component2');
    $this->assert($property->is_null() == false, 'property is exist');
    $this->assert($property->get_name() == 'component2', 'property name is component2');

    $actor2->release(); 

    $this->assert_number_and_string_property($component1);

    $property = $component1->get_property('actor2');
    $this->assert($property->is_null() == true, 'property is not exist');
    $property = $component1->get_property('component2');
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


