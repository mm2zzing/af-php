<?php
namespace af\tests\kernel\core;

use af\plugins\test\Test_Case;
use af\plugins\test\Test_Suite;
use af\kernel\core\Object;

class Object_Test extends Test_Case {
  public function test_multiple_construct() {
    $object = new Test_Object();
    $this->assert($object->log == '', 'log is empty');

    $object = new Test_Object(array('param1', 'param2', 'param3'));
    $this->assert($object->log == '', 'log is empty');

    $object = new Test_Object(array('param1'));
    $this->assert($object->log == 'construct1', 'log is construct1');

    $object = new Test_Object(array('param1', 'param2'));
    $this->assert($object->log == 'construct2', 'log is construct2');
  }

  public static function create_suite() {
    $suite = new Test_Suite('Object_Test');
    $suite->add(new Object_Test('test_multiple_construct'));
    return $suite;
  }
}
