<?php
require_once 'apdos/plugins/test/test_case.php';
require_once 'apdos/kernel/core/object_converter.php';

class Object_Converter_Test extends Test_Case {
  public function test_object_to_array() {
    $object = new stdClass();
    $object->var1 = 0;
    $object->var2 = "test";

    $array = Object_Converter::to_array($object);
    $this->assert(true == isset($array['var1']), 'var1 property is exits');
    $this->assert(true == isset($array['var2']), 'var1 property is exits');
  }

  public function test_array_to_object() {
    $array = array();
    $array['var1' ] = 0;
    $array['var2'] = 'test';

    $object = Object_Converter::to_object($array);
    $this->assert(true == isset($object->var1), 'var1 property is exist');
    $this->assert(true == isset($object->var2), 'var2 property is exist');
  }
}
