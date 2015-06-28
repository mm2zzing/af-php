<?php
namespace tests\apdos\kernel\event;

use apdos\plugins\test\Test_Case;
use apdos\plugins\test\Test_Suite;
use apdos\kernel\core\kernel;
use apdos\kernel\event\Event;
use apdos\kernel\event\Event_Dispatcher;
use apdos\kernel\event\Listener;
use tests\apdos\kernel\event\Dummy_Event;

class Event_Test extends Test_Case {
  public $occur_dispatch_event = false;
  private $event_dispatcher;

  public function __construct($method_name) {
    parent::__construct($method_name);
  }

  public function test_add_event_listener() {
    $dummy_event = new Dummy_Event(array(Dummy_Event::$DUMMY_EVENT_NAME1, 1, "1"));
    $this->event_dispatcher->dispatch_event($dummy_event);
    $this->assert(false == $this->occur_dispatch_event, "not occur dispatch event");

    $this->event_dispatcher->add_event_listener(Dummy_Event::$DUMMY_EVENT_NAME1, $this->create_event_listener());
    $this->occur_dispatch_event = false;
    $this->event_dispatcher->dispatch_event($dummy_event);
    $this->assert(true == $this->occur_dispatch_event, "occur dispatch event");
  }

  public function test_remove_event_listener() {
    $dummy_event = new Dummy_Event(array(Dummy_Event::$DUMMY_EVENT_NAME1, 1, "1"));
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

    $dummy_event = new Dummy_Event(array(Dummy_Event::$DUMMY_EVENT_NAME1, 1, "1"));
    $this->event_dispatcher->dispatch_event($dummy_event);
    $this->assert(true == $this->occur_dispatch_event, "occur dispatch event");
  } 

  public function test_async_event() {
    $this->event_dispatcher->add_event_listener(Dummy_Event::$DUMMY_EVENT_NAME1, $this->create_event_listener());

    $dummy_event = new Dummy_Event(array(Dummy_Event::$DUMMY_EVENT_NAME1, 1, "1"));
    $this->event_dispatcher->async_dispatch_event($dummy_event);
    $this->assert(false == $this->occur_dispatch_event, "do not occur dispatch event");

    $this->event_dispatcher->update();
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

  public static function create_suite() {
    $suite = new Test_Suite('Event_Test');
    $suite->add(new Event_Test('test_add_event_listener'));
    $suite->add(new Event_Test('test_remove_event_listener'));
    $suite->add(new Event_Test('test_dispatch_event'));
    $suite->add(new Event_Test('test_async_event'));
    return $suite;
  }
}

