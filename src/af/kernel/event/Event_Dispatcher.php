<?php
namespace af\kernel\event;

use af\kernel\core\Object;

class Event_Dispatcher extends Object {
  private $event_listeners = array();
  private $process_events = array();

  public function add_event_listener($event_name, $event_listener) {
    if (!isset($this->event_listeners[$event_name]))
      $this->event_listeners[$event_name] = array();
    array_push($this->event_listeners[$event_name], $event_listener);
  }

  public function remove_evnet_listenr($event_name) {
    $this->event_listeners[$event_name] = array();
  }

  public function dispatch_event($event) {
    if (!isset($this->event_listeners[$event->get_name()]))
      return;
    $listeners = $this->event_listeners[$event->get_name()];
    foreach ($listeners as $listener) {
      $listener($event);
    }
  }

  public function async_dispatch_event($event) {
    array_push($this->process_events, $event);
  }

  public function update_event() {
    foreach ($this->process_events as $event) {
      $this->dispatch_event($event);
    }
    $this->process_events = array();
  }
}
