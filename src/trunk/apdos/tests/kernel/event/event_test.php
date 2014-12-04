<?php
require_once 'apdos/plugins/test/test_case.php';
require_once 'apdos/kernel/core/kernel.php';
require_once 'apdos/kernel/event/event.php';
require_once 'apdos/kernel/event/event_dispatcher.php';
require_once 'apdos/kernel/event/listener.php';
require_once 'apdos/tests/kernel/event/dummy_event.php';

class Event_Test extends Test_Case {
  public static $DUMMY_EVENT_SERIALIZE_STRING = "{\"type\":\"Dummy_Event\",\"name\":\"dummy_event_name1\",\"data\":{\"var1\":1,\"var2\":\"1\"}}";

  public $occur_dispatch_event = false;

  private $event_dispatcher;

  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function test_serialize() {
    $dummy_event = new Dummy_Event();
    $dummy_event->init(Dummy_Event::$DUMMY_EVENT_NAME1, 1, "1");
    $string = Event::serialize($dummy_event);
    $this->assert(0 == strcmp($string, self::$DUMMY_EVENT_SERIALIZE_STRING), "seraizlie string check");
  }

  public function test_deserialize() {
    $event = Event::deserialize(self::$DUMMY_EVENT_SERIALIZE_STRING);
    $this->assert(0 == strcmp('Dummy_Event', get_class($event)), "event type is Dummy_Event");
    $this->assert(1 == $event->get_var1(), "var1 is 1");
    $this->assert(0 == strcmp("1", $event->get_var2()), "var2 is 1");
  }

  public function test_add_event_listener() {
    $dummy_event = new Dummy_Event();
    $dummy_event->init(Dummy_Event::$DUMMY_EVENT_NAME1, 1, "1");
    $this->event_dispatcher->dispatch_event($dummy_event);
    $this->assert(false == $this->occur_dispatch_event, "not occur dispatch event");

    $this->event_dispatcher->add_event_listener(Dummy_Event::$DUMMY_EVENT_NAME1, $this->create_event_listener());
    $this->occur_dispatch_event = false;
    $this->event_dispatcher->dispatch_event($dummy_event);
    $this->assert(true == $this->occur_dispatch_event, "occur dispatch event");
  }

  public function test_remove_event_listener() {
    $dummy_event = new Dummy_Event();
    $dummy_event->init(Dummy_Event::$DUMMY_EVENT_NAME1, 1, "1");
    $this->event_dispatcher->add_event_listener(Dummy_Event::$DUMMY_EVENT_NAME1, $this->create_event_listener());
    $this->event_dispatcher->dispatch_event($dummy_event);
    $this->assert(true == $this->occur_dispatch_event, "occur dispatch event");

    $this->occur_dispatch_event = false;
    $this->event_dispatcher->remove_evnet_listenr(Dummy_Event::$DUMMY_EVENT_NAME1);
    $this->event_dispatcher->dispatch_event($dummy_event);
    $this->assert(false == $this->occur_dispatch_event, "not occur dispatch event");
  }

  public function test_dispatch_event() {
    $this->event_dispatcher->add_event_listener(Dummy_Event::$DUMMY_EVENT_NAME1, $this->create_event_listener());

    $dummy_event = new Dummy_Event();
    $dummy_event->init(Dummy_Event::$DUMMY_EVENT_NAME1, 1, "1");
    $this->event_dispatcher->dispatch_event($dummy_event);
    $this->assert(true == $this->occur_dispatch_event, "occur dispatch event");
  } 

  private function create_event_listener() {
    $other = $this;
    return function($event) use(&$other) {
      $other->occur_dispatch_event = true;
    };
  }

  public function set_up() {
    $this->event_dispatcher = new Event_Dispatcher();
  }

  public function tear_down() {
    $this->event_dispatcher = null;
  }
}

