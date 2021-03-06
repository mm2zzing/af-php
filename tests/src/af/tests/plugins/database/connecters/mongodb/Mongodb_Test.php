<?php
namespace af\tests\plugins\database\connecters\mongodb;

use af\kernel\core\loader;
use af\kernel\core\kernel;
use af\plugins\test\Test_Case;
use af\plugins\test\Test_Suite;
use af\plugins\database\connecters\mongodb\Mongodb_Connecter;

class Mongodb_Test extends Test_Case {
  private $connecter;

  public function test_insert() {
    $document = array('value1'=>1, 'value2'=>'1');
    $this->connecter->select_collection('test_db')->insert($document);
    $store_document = $this->connecter->find_one(array());
    $this->assert_document($store_document, 1, '1'); 
  }

  public function test_find_one() {
    $document = array('value1'=>1, 'value2'=>'1');
    $this->connecter->select_collection('test_db')->insert($document);
    $store_document = $this->connecter->where(array('_id'=>$document['_id']))->find_one();
    $this->assert_document($store_document, 1, '1'); 
  }

  public function test_find() {
    $this->connecter->select_collection('test_db');
    $this->connecter->insert(array('value1'=>1, 'value2'=>'1'));
    $this->connecter->insert(array('value1'=>1, 'value2'=>'1'));
    $documents = $this->connecter->find();
    $this->assert(2 == count($documents), "document count is 2");
    $this->assert_document($documents[0], 1, '1');
    $this->assert_document($documents[1], 1, '1');
  }

  public function test_limit() {
    $this->connecter->select_collection('test_db');
    $this->connecter->insert(array('value1'=>1, 'value2'=>'1'));
    $this->connecter->insert(array('value1'=>1, 'value2'=>'1'));
    $this->connecter->set_limit(1);
    $documents = $this->connecter->find();
    $this->assert(1 == count($documents), "document count is 1");
  }

  public function test_skip() {
    $this->connecter->select_collection('test_db');
    $this->connecter->insert(array('value1'=>1, 'value2'=>'1'));
    $this->connecter->insert(array('value1'=>1, 'value2'=>'1'));
    $this->connecter->set_skip(1);
    $documents = $this->connecter->find();
    $this->assert(1 == count($documents), "document count is 1");
  }

  public function test_update() {
    $this->connecter->select_collection('test_db');
    $this->connecter->insert(array('value1'=>1, 'value2'=>'1'));
    $documents = $this->connecter->find();
    $this->assert_document($documents[0], 1, '1');
    $this->connecter->where(array('value1'=>1))->update(array('value1'=>2, 'value2'=>'123')); 
    $documents = $this->connecter->find();
    $this->assert_document($documents[0], 2, '123');
  }

  public function test_set() {
    $this->connecter->select_collection('test_db');
    $this->connecter->insert(array('value1'=>1, 'value2'=>'1'));
    $documents = $this->connecter->find();
    $this->assert_document($documents[0], 1, '1');
    $this->connecter->where(array('value1'=>1))->set(array('value2'=>'123'));
    $documents = $this->connecter->find();
    $this->assert_document($documents[0], 1, '123');
  }

  public function test_set_all() {
    $this->connecter->select_collection('test_db');
    $this->connecter->insert(array('value1'=>1, 'value2'=>'1'));
    $this->connecter->insert(array('value1'=>1, 'value2'=>'1'));
    $documents = $this->connecter->find();
    $this->assert_document($documents[0], 1, '1');
    $this->assert_document($documents[1], 1, '1');
    $this->connecter->where(array('value1'=>1))->set_all(array('value2'=>'123'));
    $documents = $this->connecter->find();
    $this->assert_document($documents[0], 1, '123');
    $this->assert_document($documents[1], 1, '123');
  }

  public function test_command() {
    $this->connecter->select_collection('test_db');
    $res = $this->connecter->command($query);
  }

  private function assert_document($store_document, $int_value, $string_value) {
    $this->assert($store_document['value1'] == $int_value, 'value1 expected ' . $int_value);
    $this->assert(0 == strcmp($store_document['value2'], $string_value), 'value2 expected ' . $string_value);
  }

  public function set_up() {
    $actor = Kernel::get_instance()->new_object('af\kernel\actor\Actor', '/sys/db/mongo');
    $this->connecter = $actor->add_component('af\plugins\database\connecters\mongodb\Mongodb_Connecter');
    $this->connecter->connect('mongodb://localhost:27017');
    $this->connecter->select_database('apdos_test');
    $this->connecter->drop_collection('test_db');
  }

  public function tear_down() {
    Kernel::get_instance()->delete_object('/sys/db/mongo');
  }

  public static function create_suite() {
    $suite = new Test_Suite('Mongodb_Test');
    $suite->add(new Mongodb_Test('test_insert'));
    $suite->add(new Mongodb_Test('test_find_one'));
    $suite->add(new Mongodb_Test('test_find'));
    $suite->add(new Mongodb_Test('test_limit'));
    $suite->add(new Mongodb_Test('test_skip'));
    $suite->add(new Mongodb_Test('test_update'));
    $suite->add(new Mongodb_Test('test_set'));
    $suite->add(new Mongodb_Test('test_set_all'));
    return $suite;
  }
}
