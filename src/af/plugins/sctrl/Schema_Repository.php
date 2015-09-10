<?php
namespace af\plugins\sctrl;

use af\kernel\actor\Component;
use af\kernel\actor\events\Component_Event;
use af\plugins\sctrl\errors\Schema_Error;
use af\plugins\database\connecters\mysql\MySQL_Connecter;
use af\plugins\database\connecters\mysql\MySQL_Schema;

class Schema_Repository extends Component {
  public function __construct() {
    $other = $this;
    $this->add_event_listener(Component_Event::$START, function($event) use(&$other) {
      $config = $other->get_parent()->get_component(Schema_Config::get_class_name());
      if ($config->is_null())
        throw new Schema_Error("get config is null", Schema_Error::COMPONENT_NOT_EXIST);
      $other->set_property('config', $config);

      $this->db_connecter = $other->get_parent()->add_component(MySQL_Connecter::get_class_name());
      $this->db_schema = $this->get_parent()->add_component(MySQL_Schema::get_class_name());
      $db_config = $config->get_database_config();
      $this->db_connecter->connect($db_config->host, $db_config->user, $db_config->password);
    });
  }

  /**
   * 스키마 리포지터리를 생성한다.
   *
   * @param repository_name string 리포지터티 이름
   */
  public function create_repository($repository_name) {
    $this->check_components();
    $this->db_schema->create_database($repository_name);
  }

  public function destroy_repository($repository_name) {
    $this->check_components();
    $this->db_schema->drop_database($repository_name);
  }

  private function check_components() {
    if ($this->get_property('config')->is_null())
      throw new Schema_Error('config is null', Schema_Error::COMPONENT_NOT_EXIST); 
  }

  private $db_schema;
  private $db_connecter;
}
