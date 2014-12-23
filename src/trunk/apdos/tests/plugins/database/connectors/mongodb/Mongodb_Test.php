<?php
namespace apdos\tests\plugins\database\connectors\mongodb;

use apdos\kernel\core\loader;
use apdos\kernel\core\kernel;
use apdos\plugins\test\Test_Case;
use apdos\plugins\database\connectors\mongodb\mongodb_connecter;

class Mongodb_Test extends Test_Case {
  private $connecter;

  public function test_insert() {
    $document = array('value1'=>1, 'value2'=>'1');
    $this->connecter->select_collection('ft')->insert($document);
    $store_document = $this->connecter->find_one(array());
    $this->assert_document($store_document, 1, '1'); 
  }

  public function test_find_one() {
    $document = array('value1'=>1, 'value2'=>'1');
    $this->connecter->select_collection('ft')->insert($document);
    $store_document = $this->connecter->where(array('_id'=>$document['_id']))->find_one();
    $this->assert_document($store_document, 1, '1'); 
  }

  public function test_find() {
    $this->connecter->select_collection('ft');
    $this->connecter->insert(array('value1'=>1, 'value2'=>'1'));
    $this->connecter->insert(array('value1'=>1, 'value2'=>'1'));
    $documents = $this->connecter->find();
    $this->assert(2 == count($documents), "document count is 2");
    $this->assert_document($documents[0], 1, '1');
    $this->assert_document($documents[1], 1, '1');
  }

  public function test_limit() {
    $this->connecter->select_collection('ft');
    $this->connecter->insert(array('value1'=>1, 'value2'=>'1'));
    $this->connecter->insert(array('value1'=>1, 'value2'=>'1'));
    $this->connecter->set_limit(1);
    $documents = $this->connecter->find();
    $this->assert(1 == count($documents), "document count is 1");
  }

  public function test_skip() {
    $this->connecter->select_collection('ft');
    $this->connecter->insert(array('value1'=>1, 'value2'=>'1'));
    $this->connecter->insert(array('value1'=>1, 'value2'=>'1'));
    $this->connecter->set_skip(1);
    $documents = $this->connecter->find();
    $this->assert(1 == count($documents), "document count is 1");
  }

  public function test_update() {
    $this->connecter->select_collection('ft');
    $this->connecter->insert(array('value1'=>1, 'value2'=>'1'));
    $documents = $this->connecter->find();
    $this->assert_document($documents[0], 1, '1');
    $this->connecter->where(array('value1'=>1))->update(array('value1'=>2, 'value2'=>'123')); 
    $documents = $this->connecter->find();
    $this->assert_document($documents[0], 2, '123');
  }

  public function test_set() {
    $this->connecter->select_collection('ft');
    $this->connecter->insert(array('value1'=>1, 'value2'=>'1'));
    $documents = $this->connecter->find();
    $this->assert_document($documents[0], 1, '1');
    $this->connecter->where(array('value1'=>1))->set(array('value2'=>'123'));
    $documents = $this->connecter->find();
    $this->assert_document($documents[0], 1, '123');
  }

  public function test_set_all() {
    $this->connecter->select_collection('ft');
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
    $this->connecter->select_collection('ft');
    $res = $this->connecter->command($query);
  }

  private function assert_document($store_document, $int_value, $string_value) {
    $this->assert($store_document['value1'] == $int_value, 'value1 expected ' . $int_value);
    $this->assert(0 == strcmp($store_document['value2'], $string_value), 'value2 expected ' . $string_value);
  }

  public function set_up() {
    $actor = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/sys/db/mongo');
    $this->connecter = $actor->add_component('apdos\plugins\database\connectors\mongodb\Mongodb_Connecter');
    $this->connecter->connect('mongodb://localhost:27017');
    $this->connecter->select_database('apdos_test');
    $this->connecter->drop_collection('ft');
  }

  public function tear_down() {
    Kernel::get_instance()->delete_object('/sys/db/mongo');
  }
}
