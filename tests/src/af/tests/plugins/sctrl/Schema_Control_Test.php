<?php
namespace af\tests\plugins\sctrl;

use af\plugins\test\Test_Case;
use af\plugins\test\Test_Suite;
use af\kernel\core\Kernel;
use af\kernel\actor\Actor;
use af\plugins\sctrl\Schema_Config;
use af\plugins\sctrl\Schema_Repository;
use af\plugins\database\connecters\mysql\MySQL_Schema;

class Schema_Control_Test extends Test_Case {
  const TEST_REPOSITORY = 'test_repository';
  
  public function set_up() {
    $this->helper = new Schema_Control_Test_Helper();
    $this->connecter = $this->helper->create_db_connecter();
    $this->db_schema = $this->connecter->get_component(MySQL_Schema::get_class_name());

    $this->actor = Actor::create();
    $config = $this->actor->add_component(Schema_Config::get_class_name());
    $config->load($this->helper->get_test_database_config());
    $this->repository = $this->actor->add_component(Schema_Repository::get_class_name());

    $this->actor->update();
  }

  public function tear_down() {
    $this->db_schema->drop_database(self::TEST_REPOSITORY);
    $this->connecter->release();
    $this->actor->release();
  }

  public function test_create_repository() {
    $this->assert_false($this->db_schema->has_database(self::TEST_REPOSITORY)); 
    $this->repository->create_repository(self::TEST_REPOSITORY);
    $this->assert_true($this->db_schema->has_database(self::TEST_REPOSITORY));
  }

  public function test_destroy_repository() {
    $this->assert_false($this->db_schema->has_database(self::TEST_REPOSITORY));
    $this->repository->create_repository(self::TEST_REPOSITORY);
    $this->assert_true($this->db_schema->has_database(self::TEST_REPOSITORY));
    $this->repository->destroy_repository(self::TEST_REPOSITORY);
    $this->assert_false($this->db_schema->has_database(self::TEST_REPOSITORY));
  }

  private $helper;
  private $connecter;
  private $db_schema;
  private $actor;
  private $repository;

  public static function create_suite() {
    $suite = new Test_Suite('Schema_Control_Test');
    $suite->add(new Schema_Control_Test('test_create_repository'));
    $suite->add(new Schema_Control_Test('test_destroy_repository'));
    return $suite;
  }
}
