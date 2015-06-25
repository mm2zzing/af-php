<?php
namespace tests\apdos\plugins\sharding;

use apdos\plugins\test\Test_Case;
use apdos\plugins\test\Test_Suite;
use apdos\plugins\sharding\Shard_Router;
use apdos\kernel\core\Object_Converter;

class Sharding_Test extends Test_Case {
  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function set_up() {
  }

  public function tear_down() {
  }

  public function test_insert() {
    Shard_Router::get_instance()->load(
      $this->get_shard_tables(),
      $this->get_lookup_shards(),
      $this->get_data_shards());

    Shard_Router::get_instance()->insert('mytable', $data);
  }

  public function get_shard_tables() {
    $array = array(
      array('mytable1'=>array('type'=>'sharding')),
      array('mytable2'=>array('type'=>'static', 'shard_id'=>'shard-public'))
    );
    return Object_Converter::to_object($array);
  }

  public function get_lookup_shards() {
    $array = array(
      'lookup01'=> array(
        'master'=>array('connecter'=>'mysql', 'host'=>'localhost', 'port'=>3306, 'id'=>'root', 'password'=>'', 'db_name'=>'lookup01', 'charset'=>'utf8', 'persistent'=>true),
        'slave'=>array('connecter'=>'mysql', 'host'=>'localhost', 'port'=>3306, 'id'=>'root', 'password'=>'', 'db_name'=>'lookup01', 'charset'=>'utf8', 'persistent'=>true)
      ),
      'lookup02'=> array(
        'master'=>array('connecter'=>'mysql', 'host'=>'localhost', 'port'=>3306, 'id'=>'root', 'password'=>'', 'db_name'=>'lookup02', 'charset'=>'utf8', 'persistent'=>true),
        'slave'=>array('connecter'=>'mysql', 'host'=>'localhost', 'port'=>3306, 'id'=>'root', 'password'=>'', 'db_name'=>'lookup02', 'charset'=>'utf8', 'persistent'=>true)
      )
    );
    return Object_Converter::to_object($array);
  }

  public function get_data_shards() {
    $array = array(
      'shard01'=> array(
        'master'=>array('connecter'=>'mysql', 'host'=>'localhost', 'port'=>3306, 'id'=>'root', 'password'=>'', 'db_name'=>'shard01', 'charset'=>'utf8', 'persistent'=>true),
        'slave'=>array('connecter'=>'mysql', 'host'=>'localhost', 'port'=>3306, 'id'=>'root', 'password'=>'', 'db_name'=>'shard01', 'charset'=>'utf8', 'persistent'=>true)
      ),
      'shard02'=> array(
        'master'=>array('connecter'=>'mysql', 'host'=>'localhost', 'port'=>3306, 'id'=>'root', 'password'=>'', 'db_name'=>'shard02', 'charset'=>'utf8', 'persistent'=>true),
        'slave'=>array('connecter'=>'mysql', 'host'=>'localhost', 'port'=>3306, 'id'=>'root', 'password'=>'', 'db_name'=>'shard02', 'charset'=>'utf8', 'persistent'=>true)
      ),
      'shard-public'=> array(
        'master'=>array('connecter'=>'mysql', 'host'=>'localhost', 'port'=>3306, 'id'=>'root', 'password'=>'', 'db_name'=>'shard-public', 'charset'=>'utf8', 'persistent'=>true),
        'slave'=>array('connecter'=>'mysql', 'host'=>'localhost', 'port'=>3306, 'id'=>'root', 'password'=>'', 'db_name'=>'shard-public', 'charset'=>'utf8', 'persistent'=>true)
      )
    );
    return Object_Converter::to_object($array);
  }

  public static function create_suite() {
    $suite = new Test_Suite('Sharding_Test');
    $suite->add(new Sharding_Test('test_insert'));
    return $suite;
  }
}

