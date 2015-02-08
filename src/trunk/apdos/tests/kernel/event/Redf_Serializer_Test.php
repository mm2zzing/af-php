<?php
namespace apdos\tests\kernel\event;

use apdos\plugins\test\Test_Case;
use apdos\kernel\core\kernel;
use apdos\kernel\event\Event;
use apdos\kernel\event\Event_Dispatcher;
use apdos\kernel\event\Listener;
use apdos\tests\kernel\event\Dummy_Event;
use apdos\kernel\event\serializer\Redf_Serializer;
use apdos\kernel\event\serializer\Redf_Deserializer;

class Redf_Serializer_Test extends Test_Case {
  public static $DUMMY_EVENT_SERIALIZE_STRING = "{\"type\":\"Dummy_Event\",\"name\":\"dummy_event_name1\",\"data\":{\"var1\":1,\"var2\":\"1\"}}";

  private $redf;

  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function test_serialize() {
    $dummy_event = new Dummy_Event();
    $dummy_event->init(Dummy_Event::$DUMMY_EVENT_NAME1, 1, "1");
    $redf = new Redf_Serializer();
    $string = $redf->write($dummy_event);
    $this->assert(0 == strcmp($string, self::$DUMMY_EVENT_SERIALIZE_STRING), "seraizlie string check");
  }

  public function test_deserialize() {
    $redf = new Redf_Deserializer();
    $event = $redf->read(self::$DUMMY_EVENT_SERIALIZE_STRING);
    $this->assert(0 == strcmp('apdos\tests\kernel\event\Dummy_Event', get_class($event)), "event type is Dummy_Event");
    $this->assert(1 == $event->get_var1(), "var1 is 1");
    $this->assert(0 == strcmp("1", $event->get_var2()), "var2 is 1");
  }

  public function set_up() {
  }

  public function tear_down() {
  }
}

