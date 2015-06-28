<?php
namespace tests\apdos\kernel\core;

use apdos\plugins\test\Test_Case;
use apdos\plugins\test\Test_Suite;
use apdos\kernel\core\Object;

class Object_Test extends Test_Case {
  public function test_multiple_constructor() {
    $object = new Test_Object();
    $this->assert($object->log == '', 'log is empty');

    $object = new Test_Object(array('param1', 'param2', 'param3'));
    $this->assert($object->log == '', 'log is empty');

    $object = new Test_Object(array('param1'));
    $this->assert($object->log == 'constructor1', 'log is constructor1');

    $object = new Test_Object(array('param1', 'param2'));
    $this->assert($object->log == 'constructor2', 'log is constructor2');
  }

  public static function create_suite() {
    $suite = new Test_Suite('Object_Test');
    $suite->add(new Object_Test('test_multiple_constructor'));
    return $suite;
  }
}
