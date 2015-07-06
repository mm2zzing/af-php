<?php
namespace apdos\plugins\database\base\rdb;

use apdos\kernel\actor\Component;
use apdos\kernel\actor\events\Component_Event;
use apdos\kernel\actor\property\errors\Property_Error;
use apdos\plugins\database\base\rdb\errors\RDB_Error;

/**
 * @class RDB_Session
 *
 * @brief 데이터베이스 커넥터, 스키마, 유틸 객체를 생성하고 조회하는 일을 
          데이터베이스 세션이라는 컴포넌트로 래핑
 * @author Lee, Hyeon-gi
 */
class RDB_Session extends Component {
  public function __construct() {
    $that = $this;
    $this->add_event_listener(Component_Event::$START, function($event) use(&$that) {
      $that->build_components();
    });
  }

  private function build_components() {
    $this->get_parent()->add_component($this->get_connecter_class_name());
    $this->get_parent()->add_component($this->get_schema_class_name());
    $this->get_parent()->add_component($this->get_util_class_name());
  }

  public function get_connecter() {
    $result = $this->get_component($this->get_connecter_class_name());
    if ($result->is_null())
      throw new RDB_Error('Connecter is null', RDB_Error::CONNECTER_IS_NULL);
    return $result;
  }

  public function get_schema() {
    $result = $this->get_component($this->get_schema_class_name());
    if ($result->is_null())
      throw new RDB_Error('Shema is null', RDB_Error::SCHEMA_IS_NULL);
    return $result;
  }

  public function get_util() {
    $result = $this->get_component($this->get_util_class_name());
    if ($result->is_null())
      throw new RDB_Error('Shema is null', RDB_Error::SCHEMA_IS_NULL);
    return $result;
  }

  private function get_connecter_class_name() {
    $property = $this->get_property('connecter_class_name');
    if ($property->is_null())
      throw new Property_Error($property, Property_Error::PROPERTY_IS_EMPTY);
    return $property->get_value();
  }

  private function get_schema_class_name() {
    $property = $this->get_property('schema_class_name');
    if ($property->is_null())
      throw new Property_Error($property, Property_Error::PROPERTY_IS_EMPTY);
    return $property->get_value();

  }

  private function get_util_class_name() {
    $property = $this->get_property('util_class_name');
    if ($property->is_null())
      throw new Property_Error($property, Property_Error::PROPERTY_IS_EMPTY);
    return $property->get_value();

  }
}

