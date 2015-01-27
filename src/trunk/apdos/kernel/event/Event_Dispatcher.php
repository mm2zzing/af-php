<?php
namespace apdos\kernel\event;

class Event_Dispatcher {
  private $event_listeners = array();

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
}
